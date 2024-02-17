<?php

namespace App\Sensor;

use App\Record;
use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg implements Sensor
{
    public function config(): SensorConfig 
    {
        return new SensorConfig("loadavg", "loadavg");
    }
    
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $threshold = $serverinfo->cpuinfo()["threads"];
        $report = (new Report())->setTitle("Load Average");
        
        $current_load = $this->parse($records->last()->data);
        $report->setHTML(view("agent.loadavg", ["current_load" => $current_load]));
        
        $max_load = $records
                ->map(function (Record $record) {
                    $this->parse($record->data);
                })
                ->max();
        
        if ($max_load > 2 * $threshold) {
            return $report->setStatus(Status::error());
        }

        if ($max_load > $threshold) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }

    public function loadPoints(Collection $records)
    {
        $points = [];
        foreach ($records as $record) {
            $points[] = new Point(
                $record->time * 1000,
                $this->parse($record->data)
            );
        }
        return $points;
    }

    public function parse(string $string) : string
    {
        return current(explode(" ", $string));
    }
}
