<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg extends AbstractSensor {



    public function report() {
        return "<p>Current load: " . $this->getLastValue() . "</p>";
    }

    public function status() {
        return self::STATUS_OK;
    }

    public function getLastValue() {
        $record = $this->getLastRecord("loadavg")->loadavg;
        return $this->parse($record);
    }

    function parse($string) {
        return current(explode(" ", $string));
    }
}
