<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
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
    public function config(): SensorConfig 
    {
        return new SensorConfig("usb-temperature", "TEMPer");
    }

    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("USB Temperature");
        
        $record = $records->last();
        $temper = new Temper();
        $value = $temper->convert($record->data);
        $report->setHTML("<p>Ambient temperature (USB TEMPer) : $value °C</p>");
        
        $report->setStatus(Status::ok());
        return $report;
    }
}
