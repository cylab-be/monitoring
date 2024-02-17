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
class Reboot implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig("reboot", "reboot");
    }
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("Reboot required");
        
        $record = $records->last();
        if ($record->data) {
            return $report->setStatus(Status::warning())
                    ->setHTML("<p>Reboot required: yes</p>");
        }

        return $report->setStatus(Status::ok())
                ->setHtml("<p>Reboot required: no</p>");
    }
}
