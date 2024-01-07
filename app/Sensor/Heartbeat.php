<?php

namespace App\Sensor;

use App\Sensor;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

use Carbon\Carbon;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Heartbeat implements Sensor
{
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("Heartbeat");
        
        $record = $records->last();
        $report->setHTML("<p>Last heartbeat received "
            . Carbon::createFromTimestamp($record->time)->diffForHumans() . "</p>");
        
        $delta = \time() - $record->time;
        // > 15 minutes
        if ($delta > 900) {
            return $report->setStatus(Status::error());
        }
        
        return $report->setStatus(Status::ok());
    }
}
