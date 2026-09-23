<?php

namespace App\Sensor\Linux;

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
class USBtemperature extends Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "usb_temperature",
            "TEMPer",
            "Uses hid-query to read the value of a TEMPer USB device",
            ["TEMPer" => "command -v hid-query >/dev/null 2>&1 && "
                . "hid-query /dev/hidraw1 0x01 0x80 0x33 0x01 0x00 0x00 0x00 0x00"]
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("USB Temperature");

        $value = $this->convert($record->data);
        $report->setHTML("<p>Ambient temperature (USB TEMPer) : $value °C</p>");

        $report->setStatus(Status::ok());
        return $report;
    }
    
    public function convert(string $string) : float
    {
        // allows to extract device response
        // 80 80 09 47 4e 20 00 00
        $REGEX = "/^80\s80\s([0-9a-fA-F]{2}\s[0-9a-fA-F]{2})/m";
        
        // extract 2 hex values from device response
        // 09 47
        $values = [];
        preg_match($REGEX, $string, $values);
        
        // remove intermediate white space
        // 0947
        $hexatemp = preg_replace("/\s+/", "", $values[1]);
        
        // convert to decimal
        // 2375
        $decitemp = hexdec($hexatemp);
        
        return $decitemp / 100.0;
    }
}
