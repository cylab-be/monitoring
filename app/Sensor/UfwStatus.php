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
class UfwStatus extends Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ufw_status",
            "ufw_status",
            "Check if UFW is active"
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("UFW status")
                ->setHTML($record->data);

        if ($record->data == "Status: inactive") {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }
}
