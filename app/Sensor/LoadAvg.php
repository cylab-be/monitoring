<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

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
    
    
    public function analyze(Record $record): Report
    {
        $server = $record->server;
        
        $threshold = $server->info()->cpuinfo()["threads"];
        $report = (new Report())->setTitle("Load Average");
        
        $current_load = $this->parse($record->data);
        $report->setHTML(view("agent.loadavg", ["current_load" => $current_load]));
        
        $records = $server->lastRecords($record->label);
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
