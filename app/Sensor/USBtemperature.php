<?php

namespace App\Sensor;

/**
     * Description of USBTemperature
 *
 * @author helha
 */
class USBtemperature extends \App\AbstractSensor
{
    //get device responce (8 bytes) :
    const REGEXP = "/^(80 80)\s*([A-z\/0-9]+) \s*([A-z\/0-9]+)/m";

    public function report() : string
    {
        $record = $this->getServer()->lastRecord();
        if (! isset($record["TEMPer"])) {
            return "<p>No data available...</p>"
                . "<p>Maybe <code>TEMPer</code> is not installed.</p>"
                . "<p>You can install it following the tutorial on the Gitlab repository</p>";
        }
        $temper = self::parse($record['TEMPer']);
        $return= "<p>Ambient temperature (USB TEMPer) : " . $temper->temp[1] . "." . $temper->temp[2] . " °C " . "</p>";
        return $return;
    }

    public function status() : int
    {
        $record = $this->getServer()->lastRecord();
        if (! isset($record["TEMPer"])) {
            return self::STATUS_UNKNOWN;
        }
        $status = self::STATUS_OK;
        $USBTemp = self::parse($record['TEMPer']);
        if ((int)($USBTemp->temp[1]) > 60) {
            $status = self::STATUS_WARNING;
        }
        return $status;
    }

    public static function parse(string $string)
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values); //get 8 bytes response from TEMPerUSB device
        $USBTemp = new Temper();
        $USBTemp->part1 = implode($values[2]);
        $USBTemp->part2 = implode($values[3]);
        $USBTemp->conversion(); //1st element = integer part, 2th = decimal part
        $temper=$USBTemp;
        return $temper;
    }
}
