<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

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
    
    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Time drift");
        /** @var \App\Record $record */
        
        $delta = (int) $record->data - $record->time;
        $report->setHTML("<p>Time drift: $delta seconds</p>");
        
        if (abs($delta) > 10) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }
}
