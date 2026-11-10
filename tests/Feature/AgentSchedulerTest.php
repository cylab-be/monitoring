<?php

namespace Tests\Feature;

use App\AgentScheduler;
use App\Jobs\RunAgent;
use App\Notification;
use App\Organization;
use App\Record;
use App\Report;
use App\ReportSummary;
use App\Server;
use App\Status;
use App\StatusChange;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

/**
 * Tests the agent scheduling pipeline:
 * Record -> notify -> RunAgent jobs, Report -> ReportSummary -> StatusChange -> Notification.
 *
 * @author tibo
 */
class AgentSchedulerTest extends TestCase
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

    /**
     * Create a report row directly, without triggering the notification pipeline.
     */
    private function rawReport(int $server_id, string $label, int $time, int $status_code) : int
    {
        return DB::table('reports')->insertGetId([
            "time" => $time,
            "server_id" => $server_id,
            "label" => $label,
            "status_code" => $status_code,
            "title" => "test",
            "html" => "<p>test</p>",
        ]);
    }

    public function testThrottlingThresholdScalesWithServerCount()
    {
        $scheduler = AgentScheduler::get();
        $sensorCount = $scheduler->sensors()->count();
        $this->assertGreaterThan(0, $sensorCount);

        // with no server, the "max(2, ...)" floor applies
        $this->assertEquals(2 * 2 * $sensorCount, $scheduler->throttlingTreshold());

        // one server does not change the threshold (floor is 2)
        $this->createServer("srv01");
        $this->assertEquals(2 * 2 * $sensorCount, $scheduler->throttlingTreshold());

        // with more than 2 servers, the count takes over
        $this->createServer("srv02");
        $this->createServer("srv03");
        $this->assertEquals(3 * 2 * $sensorCount, $scheduler->throttlingTreshold());
    }

    public function testNotifyDispatchesOneJobPerMatchingAgent()
    {
        $server = $this->createServer();
        Queue::fake();

        $record = new Record();
        $record->server_id = $server->id;
        $record->label = "disks";

        $agents = AgentScheduler::get()->agentsForLabel("disks");
        $this->assertGreaterThan(0, $agents->count());

        AgentScheduler::get()->notify($record);

        Queue::assertPushedTimes(RunAgent::class, $agents->count());
        $this->assertCount(
            $agents->count(),
            Queue::pushed(RunAgent::class, function (RunAgent $job) use ($record) {
                return $job->record == $record;
            })
        );
    }

    public function testNotifyDoesNothingForUnknownLabel()
    {
        $this->createServer();
        Queue::fake();

        $record = new Record();
        $record->label = "does-not-exist";

        AgentScheduler::get()->notify($record);

        Queue::assertNotPushed(RunAgent::class);
    }

    public function testNotifyIsSkippedWhenQueueIsAboveThreshold()
    {
        $this->createServer();
        Queue::fake();

        $threshold = AgentScheduler::get()->throttlingTreshold();
        // fill the (fake) queue above the threshold
        for ($i = 0; $i <= $threshold; $i++) {
            Queue::push(new class {
                public function handle()
                {
                }
            });
        }

        $record = new Record();
        $record->label = "disks";
        AgentScheduler::get()->notify($record);

        // only the dummy jobs must be present in the queue
        Queue::assertNotPushed(RunAgent::class);
        $this->assertEquals($threshold + 1, Queue::size());
    }

    public function testLastReportOfPicksMostRecentWithinLast24h()
    {
        $server = $this->createServer();
        $now = time();

        $older = $this->rawReport($server->id, "disks", $now - 7200, 10);
        $recent = $this->rawReport($server->id, "disks", $now - 3600, 0);
        $this->rawReport($server->id, "memory", $now - 3 * 86400, 0); // outside the 24h window

        $scheduler = AgentScheduler::get();

        $this->assertEquals($recent, $scheduler->lastReportOf($server, "disks")->id);
        $this->assertNotEquals($older, $scheduler->lastReportOf($server, "disks")->id);

        // labels with no recent report return null
        $this->assertNull($scheduler->lastReportOf($server, "memory"));

        // lastReportsOf returns one (the most recent) report per known label
        $reports = $scheduler->lastReportsOf($server);
        $this->assertCount(1, $reports);
        $this->assertEquals($recent, $reports->first()->id);
    }

    public function testSavingReportCreatesSummaryWithMaxStatus()
    {
        $server = $this->createServer();

        // an OK report first...
        $ok = new Report();
        $ok->server_id = $server->id;
        $ok->label = "memory";
        $ok->time = time();
        $ok->setTitle("Memory : Usage");
        $ok->setStatus(Status::ok());
        $ok->setHTML("<p>mem</p>");
        $ok->save();

        // ...then a warning report.
        $warning = new Report();
        $warning->server_id = $server->id;
        $warning->label = "disks";
        $warning->time = time();
        $warning->setTitle("Storage : Partitions");
        $warning->setStatus(Status::warning());
        $warning->setHTML("<p>disk</p>");
        $warning->save();

        // one summary is created per report...
        $summaries = ReportSummary::where("server_id", $server->id)->orderByDesc("id")->get();
        $this->assertCount(2, $summaries);

        // ...and the latest one aggregates the most recent reports of every sensor
        $summary = $summaries->first();
        $this->assertEquals(Status::WARNING, $summary->status_code);
        $this->assertContains($warning->id, $summary->reports);
        $this->assertContains($ok->id, $summary->reports);
        $this->assertGreaterThan(0, $summary->time);
    }

    public function testSavingSummaryTracksStatusChanges()
    {
        $server = $this->createServer();

        // first summary: a StatusChange must be recorded (new server)
        $this->saveSummary($server->id, Status::ok());
        $changes = StatusChange::where("server_id", $server->id)->orderBy("id")->get();
        $this->assertCount(1, $changes);
        $this->assertEquals(Status::OK, $changes->first()->status);
        // no notification for a non-error status
        $this->assertCount(0, Notification::where("server_id", $server->id)->get());

        // same status: no new change
        $this->saveSummary($server->id, Status::ok());
        $this->assertCount(1, StatusChange::where("server_id", $server->id)->get());

        // status changes to error: a new StatusChange is recorded
        // and a notification is sent.
        $this->saveSummary($server->id, Status::error());
        $changes = StatusChange::where("server_id", $server->id)->orderBy("id")->get();
        $this->assertCount(2, $changes);
        $this->assertEquals(Status::ERROR, $changes->last()->status);

        $notifications = Notification::where("server_id", $server->id)->get();
        $this->assertCount(1, $notifications);
        $this->assertEquals("change", $notifications->first()->type);
        $this->assertEquals($changes->last()->id, $notifications->first()->change_id);
    }

    private function saveSummary(int $server_id, Status $status) : ReportSummary
    {
        $summary = new ReportSummary();
        $summary->server_id = $server_id;
        $summary->time = time();
        $summary->status_code = $status->code();
        $summary->reports = [];
        $summary->save();

        return $summary;
    }
}
