<?php

namespace App\Sensor;

use App\Sensor;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Date implements Sensor
{
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("Time drift");
        /** @var \App\Record $last_record */
        $last_record = $records->last();
        
        if (! isset($last_record->data["date"])) {
            return $report->setHTML("<p>No data available ...</p>");
        }
        
        $delta = $last_record->data["date"] - $last_record->time;
        $report->setHTML("<p>Time drift: $delta seconds</p>");
        
        if (abs($delta) > 10) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }
}
