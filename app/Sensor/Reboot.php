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
class Reboot implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
                "reboot",
                "reboot",
                "Check if /var/run/reboot-required exists (on Debian based distros)");
    }
    
    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Reboot required");
        
        if ($record->data) {
            return $report->setStatus(Status::warning())
                    ->setHTML("<p>Reboot required: yes</p>");
        }

        return $report->setStatus(Status::ok())
                ->setHtml("<p>Reboot required: no</p>");
    }
}
