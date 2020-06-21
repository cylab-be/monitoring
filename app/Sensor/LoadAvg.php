<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg extends AbstractSensor
{

    public function report() : string
    {
        return view("agent.loadavg", [
            "current_load" => $this->getLastValue(),
            "server" => $this->getServer()]);
    }

    public function loadPoints()
    {
        $records = $this->getServer()->lastRecords("loadavg", 288);

        $points = [];
        foreach ($records as $record) {
            $points[] = new Point(
                $record->time * 1000,
                $this->parse($record->loadavg)
            );
        }
        return $points;
    }

    public function status() : int
    {
        return self::STATUS_OK;
    }

    public function getLastValue()
    {
        $record = $this->getServer()->lastRecord();
        if (! isset($record['loadavg'])) {
            return "<p>No data available...</p>";
        }
        $field = $record->loadavg;
        return $this->parse($field);
    }

    public function parse($string)
    {
        return current(explode(" ", $string));
    }
}
