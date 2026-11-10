<?php

namespace Tests\Feature;

use App\FailedJob;
use App\Jobs\CleanOldData;
use App\Jobs\RunAgent;
use App\Jobs\TriggerHeartbeatAgents;
use App\Organization;
use App\Record;
use App\Report;
use App\ReportSummary;
use App\Server;
use App\Status;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

/**
 * Tests the background jobs: RunAgent, CleanOldData and TriggerHeartbeatAgents.
 *
 * @author tibo
 */
class JobsTest extends TestCase
{
    use RefreshDatabase;

    private function createServer(string $name = "srv01") : Server
    {
        $organization = new Organization();
        $organization->name = "TEST";
        $organization->save();

        $server = new Server();
        $server->name = $name;
        $server->properties = [];
        $organization->servers()->save($server);

        return $server;
    }

    public function testRunAgentSavesReport()
    {
        Queue::fake();

        $server = $this->createServer();

        $record = new Record();
        $record->server_id = $server->id;
        $record->label = "disks";
        $record->time = time();
        $record->data = file_get_contents(__DIR__ . "/../Unit/df");
        $record->save();

        $job = new RunAgent(new \App\Sensor\Disks(), $record);
        $job->handle();

        $report = Report::where("server_id", $server->id)
                ->where("label", "disks")
                ->first();
        $this->assertNotNull($report);
        $this->assertEquals($record->id, $report->record_id);
        $this->assertEquals("Storage : Partitions", $report->title());
        $this->assertEquals(Status::OK, $report->status_code);
        $this->assertGreaterThan(0, $report->time);

        // the HTML must be rendered from the sensor blade view
        $this->assertStringContainsString("/dev/sda1", $report->html);

        // saving the report must have triggered the summary pipeline
        $this->assertCount(1, ReportSummary::where("server_id", $server->id)->get());
    }

    public function testRunAgentWithServerInfoParserUpdatesServerInfo()
    {
        Queue::fake();

        $server = $this->createServer();

        $record = new Record();
        $record->server_id = $server->id;
        $record->label = "cpu";
        $record->time = time();
        $record->data = file_get_contents(__DIR__ . "/../Unit/cpuinfo");
        $record->save();

        $job = new RunAgent(new \App\Sensor\DeviceInfo\CPU(), $record);
        $job->handle();

        // ServerInfoParsers update the device info...
        $info = $server->info->fresh();
        $this->assertEquals(8, $info->cpuinfo["threads"]);

        // ...but produce no report.
        $this->assertCount(0, Report::where("server_id", $server->id)->get());
    }

    public function testCleanOldDataKeepsRecentAndDeletesOld()
    {
        $server = $this->createServer();

        $now = time();
        $old = $now - 3600 * 24 * 8; // 8 days ago, outside the 7 days retention

        DB::table('records')->insert([
            ["server_id" => $server->id, "label" => "disks", "data" => "old", "time" => $old],
            ["server_id" => $server->id, "label" => "disks", "data" => "new", "time" => $now],
        ]);

        DB::table('reports')->insert([
            ["time" => $old, "server_id" => $server->id, "label" => "disks",
             "status_code" => 0, "title" => "old", "html" => "old"],
            ["time" => $now, "server_id" => $server->id, "label" => "disks",
             "status_code" => 0, "title" => "new", "html" => "new"],
        ]);

        DB::table('report_summaries')->insert([
            ["server_id" => $server->id, "time" => $old, "status_code" => 0, "reports" => "[]"],
            ["server_id" => $server->id, "time" => $now, "status_code" => 0, "reports" => "[]"],
        ]);

        DB::table('failed_jobs')->insert([
            ["connection" => "redis", "queue" => "default", "payload" => "{}",
             "exception" => "old", "failed_at" => Carbon::createFromTimestamp($old)],
            ["connection" => "redis", "queue" => "default", "payload" => "{}",
             "exception" => "new", "failed_at" => Carbon::now()],
        ]);

        (new CleanOldData())->handle();

        $this->assertEquals("new", DB::table('records')->soleValue("data"));
        $this->assertEquals("new", DB::table('reports')->soleValue("title"));
        $this->assertCount(1, ReportSummary::all());
        $this->assertCount(1, FailedJob::all());
    }

    public function testTriggerHeartbeatAgentsOnlyForServersWithRecords()
    {
        Queue::fake();

        $withRecord = $this->createServer("with-record");
        $withoutRecord = $this->createServer("without-record");

        // insert the record directly to avoid triggering the agent pipeline
        DB::table('records')->insert([
            "server_id" => $withRecord->id,
            "label" => "disks",
            "data" => "x",
            "time" => time(),
        ]);

        (new TriggerHeartbeatAgents())->handle();

        // only the server with a record must get a heartbeat agent
        $this->assertCount(
            1,
            Queue::pushed(RunAgent::class, function (RunAgent $job) use ($withRecord) {
                return $job->record->server_id == $withRecord->id
                    && $job->agent instanceof \App\Sensor\Heartbeat;
            })
        );
        Queue::assertNotPushed(RunAgent::class, function (RunAgent $job) use ($withoutRecord) {
            return $job->record->server_id == $withoutRecord->id;
        });
    }
}
