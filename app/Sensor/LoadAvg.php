<?php

namespace App\Sensor;

use \App\Sensor;
use \App\Status;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg extends Sensor
{

    /**
     *
     * @param array<\App\Record> $records
     * @return string
     */
    public function report(array $records) : string
    {
        $record = end($records);
        if (! isset($record->data['loadavg'])) {
            return "<p>No data available...</p>";
        }
        $current_load = $this->parse($record->data["loadavg"]);

        return view(
            "agent.loadavg",
            ["current_load" => $current_load]
        );
    }

    public function loadPoints(array $records)
    {
        $points = [];
        foreach ($records as $record) {
            $points[] = new Point(
                $record->time * 1000,
                $this->parse($record->data["loadavg"])
            );
        }
        return $points;
    }

    public function status(array $records) : int
    {
        $threshold = $this->server()->info()->cpuinfo()["threads"];
        
        $max = 0;
        foreach ($records as $record) {
            $load = $this->parse($record->data["loadavg"]);
            if ($load > $max) {
                $max = $load;
            }
        }
        
        if ($max > 2 * $threshold) {
            return Status::ERROR;
        }

        if ($max > $threshold) {
            return Status::WARNING;
        }

        return Status::OK;
    }

    public function parse(string $string) : string
    {
        return current(explode(" ", $string));
    }
}
