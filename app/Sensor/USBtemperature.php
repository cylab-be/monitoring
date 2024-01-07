<?php

namespace App\Sensor;

use App\Sensor;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
     * Description of USBTemperature
 *
 * @author helha
 */
class USBtemperature implements Sensor
{

    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("USB Temperature");
        
        $record = $records->last();
        if (! isset($record->data["TEMPer"])) {
            return $report->setHTML("<p>No data available...</p>"
                . "<p>Maybe <code>TEMPer</code> is not installed? "
                . "You can install it following the tutorial on the Gitlab repository</p>");
        }
        
        $temper = new Temper();
        $value = $temper->convert($record->data['TEMPer']);
        $report->setHTML("<p>Ambient temperature (USB TEMPer) : $value °C " . "</p>");
        
        $report->setStatus(Status::ok());
        return $report;
    }
}
