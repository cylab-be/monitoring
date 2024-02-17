<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Ssacli
 *
 * @author tibo
 */
class Perccli implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig("perccli", "perccli");
    }
    
    const REGEXP = "/(\d+:\d+)\s+\d+\s+(\w+)\s+\d+\s+(.*(GB|TB))\s+\w+\s+(\w+)/";

    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("DELL perccli");
        
        $record = $records->last();
        $drives = $this->parse($record->data);
        $report->setHTML(view("sensor.perccli", ["drives" => $drives]));
        
        return $report->setStatus(Status::max($drives));
    }

    /**
     *
     * @param string $string
     * @return array \App\Sensor\PerccliDrive
     */
    public function parse($string)
    {
        $values = [];
        preg_match_all(self::REGEXP, $string, $values);

        $drives = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $drive = new PerccliDrive();
            $drive->slot = $values[1][$i];
            $drive->type = $values[5][$i];
            $drive->size = $values[3][$i];
            $drive->status = ($values[2][$i] == "Onln") ? Status::ok() : Status::warning();
            $drives[] = $drive;
        }
        return $drives;
    }
}
