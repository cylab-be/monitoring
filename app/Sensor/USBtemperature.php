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
    const REGEXP = "^(80 80)\s*([A-z\/0-9]+) \s*([A-z\/0-9]+)/m";

    public function report()
    {
        $record = $this->getLastRecord("TEMPer");
        if ($record == null) {
            return "<p>No data available...</p>"
                . "<p>Maybe <code>TEMPer</code> is not installed.</p>"
                . "<p>You can install it following the tutorial on the Gitlab repository</p>";
        }
        $temper = self::parse($record['TEMPer']);
        $return= "<p>Ambient temperature (USB TEMPer) : "
        . $temper[1] . "." . $temper[2] . " °C " . "</p>";
        return $return;
    }

    public function status()
    {
        $record = $this->getLastRecord("TEMPer");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $all_status = [];
        foreach (self::parse($record['TEMPer']) as $USBTemp) {
            /* @var $USBTemp Temper */
            $status = self::STATUS_OK;
            if ($USBTemp->temperature[1] > 75) {
                $status = self::STATUS_WARNING;
            }
            $all_status[] = $status;
        }

        return max($all_status);
    }

    public static function parse(string $string)
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values); //get 8 bytes response from TEMPerUSB device
        $temperatures = array();
        $USBTemp = new Temper();
        $USBTemp->part1 = $values[2];
        $USBTemp->part2 = $values[3];
        $temper[] = $USBTemp->conversion(); //1st element = integer part, 2th = decimal part
        return $temper;
    }
}
