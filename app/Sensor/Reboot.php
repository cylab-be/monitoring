<?php

namespace App\Sensor;

use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Reboot implements \App\Sensor
{
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("Reboot required");
        
        $record = $records->last();
        
        if (! isset($record->data['reboot'])) {
            return $report->setStatus(Status::unknown())
                    ->setHTML("<p>No data available!</p>");
        }

        if ($record->data["reboot"]) {
            return $report->setStatus(Status::warning())
                    ->setHTML("<p>Reboot required: yes</p>");
        }

        return $report->setStatus(Status::ok())
                ->setHtml("<p>Reboot required: no</p>");
    }
}
