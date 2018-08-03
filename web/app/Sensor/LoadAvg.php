<?php

namespace App\Sensor;

use \App\AbstractSensor;


class Point {
    public $t = 0;
    public $y = 0;

    public function __construct($t, $y) {
        $this->t = $t;
        $this->y = $y;
    }
}

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg extends AbstractSensor {

    public function report() {

        $records = $this->getLastRecords("loadavg", 288);
        $points = [];

        foreach ($records as $record) {
            $points[] = new Point($record->time * 1000, $this->parse($record->loadavg));
        }

        return view("agent.loadavg", [
            "current_load" => $this->getLastValue(),
            "points" => $points]);
    }

    public function status() {
        return self::STATUS_OK;
    }

    public function getLastValue() {
        $record = $this->getLastRecord("loadavg");
        if ($record == null) {
            return "no data...";
        }
        $field = $record->loadavg;
        return $this->parse($field);
    }

    function parse($string) {
        return current(explode(" ", $string));
    }
}
