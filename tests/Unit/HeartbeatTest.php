<?php

namespace Tests\Unit;

use App\Record;
use App\Sensor\Heartbeat;
use App\Status;

use Tests\TestCase;

/**
 * Description of HeartbeatTest
 *
 * @author tibo
 */
class HeartbeatTest extends TestCase
{
    private function record(int $time) : Record
    {
        $record = new Record();
        $record->time = $time;
        return $record;
    }

    public function testFreshHeartbeatIsOk()
    {
        $sensor = new Heartbeat();
        $report = $sensor->analyze($this->record(time() - 10));

        $this->assertEquals("Heartbeat", $report->title());
        $this->assertEquals(Status::OK, $report->status_code);
        $this->assertStringContainsString("Last heartbeat received", $report->html);
    }

    public function testStaleHeartbeatIsError()
    {
        $sensor = new Heartbeat();

        // last heartbeat more than 15 minutes ago
        $report = $sensor->analyze($this->record(time() - 3600));

        $this->assertEquals(Status::ERROR, $report->status_code);
    }

    public function testConfig()
    {
        $config = (new Heartbeat())->config();

        $this->assertEquals("heartbeat", $config->label);
        // the heartbeat agent is not triggered by a specific label
        $this->assertEquals("will-be-triggered-by-scheduler", $config->trigger_label);
    }
}
