<?php

namespace App\Sensor;

/**
     * Description of Update
 *
 * @author helha
 */
class CPUtemperature extends \App\AbstractSensor
{

    const REGEXP = "/^(Core \d+):\s+\+(\d+\.\d+)/m"; ///^(Core\s\d):\s+\/m

    public function report()
    {
        $record = $this->getLastRecord("cputemperature");
        if ($record == null) {
            return "<p>No data available...</p>"
                . "<p>Maybe <code>sensors</code> is not installed.</p>"
                . "<p>You can install it with <code>sudo apt install lm-sensors</code></p>";
        }

        $CPUTEMPS = self::parse($record->cputemperature);
        $return = "<table class='table table-sm'>";
        $return .= "<tr><th>Name</th><th>Temperature (°C)</th></tr>";
        foreach ($CPUTEMPS as $CPUTemp) {
            $return .= "<tr><td>" . $CPUTemp->name . "</td><td>"
                    . $CPUTemp->value  . "</td></tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status()
    {
        $record = $this->getLastRecord("cputemperature");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $all_status = [];
        foreach (self::parse($record->cputemperature) as $CPUTemp) {
            /* @var $CPUTemp Temperature */
            $status = self::STATUS_OK;
            if ($CPUTemp->value > 100) {
                $status = self::STATUS_WARNING;
            }
            $all_status[] = $status;
        }

        return max($all_status);
    }

    public static $skip_fs = ["none"];

    public static function parse(string $string)
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $CPUTEMPS = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $fs = $values[1][$i];
            if (in_array($fs, self::$skip_fs)) {
                continue;
            }

            $CPUTemp = new Temperature();
            $CPUTemp->name = $fs;
            $CPUTemp->value = $values[2][$i];
            $CPUTEMPS[] = $CPUTemp;
        }
        return $CPUTEMPS;
    }
}
