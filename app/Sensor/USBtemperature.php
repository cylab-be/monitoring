<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
     * Description of USBTemperature
 *
 * @author helha
 */
class USBtemperature implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "usb-temperature",
            "TEMPer",
            "Uses hid-query to read the value of a TEMPer USB device"
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("USB Temperature");
        
        $temper = new Temper();
        $value = $temper->convert($record->data);
        $report->setHTML("<p>Ambient temperature (USB TEMPer) : $value °C</p>");
        
        $report->setStatus(Status::ok());
        return $report;
    }
}
