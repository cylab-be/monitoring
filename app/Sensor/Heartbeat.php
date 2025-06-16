<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Carbon\Carbon;

/**
 * Checks when the last record (ping) was received from server.
 * This is a special agent that is triggered on a time basis.
 * See App\Jobs\TriggerHeartbeatAgents
 *
 * @author tibo
 */
class Heartbeat extends Sensor
{
    //put your code here
    public function analyze(Record $record): Report
    {
        $report = new Report();
        $report->setTitle("Heartbeat");
        
        $delta = time() - $record->time;
        
        $report->setHTML(
            "<p>Last heartbeat received " .
                Carbon::createFromTimestamp($record->time)->diffForHumans() .
            "</p>"
        );

        // > 15 minutes
        if ($delta > 900) {
            return $report->setStatus(Status::error());
        }

        return $report->setStatus(Status::ok());
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "heartbeat",
            "will-be-triggered-by-scheduler",
            "Check when the last info was received from server"
        );
    }
}
