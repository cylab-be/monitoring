<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
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
    public function config(): SensorConfig
    {
        return new SensorConfig("date", "date");
    }
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("Time drift");
        /** @var \App\Record $last_record */
        $last_record = $records->last();
        
        $delta = (int) $last_record->data - $last_record->time;
        $report->setHTML("<p>Time drift: $delta seconds</p>");
        
        if (abs($delta) > 10) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }
}
