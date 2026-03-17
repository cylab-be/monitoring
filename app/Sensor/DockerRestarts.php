<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Report;
use App\Record;
use App\Status;

/**
 * Description of SystemdFailedUnits
 *
 * @author tibo
 */
class DockerRestarts extends Sensor
{
    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "docker-restarts",
            "docker-restarts",
            "Detecting Docker containers with many restart"
        );
    }
    
    #[\Override]
    public function analyze(Record $record): ?\App\Report
    {
        $report = (new Report)->setTitle("Docker restart");
        $report->setStatus(Status::ok());
        $report->setHTML("<pre>" . $record->data . "</pre>");
        
        $restarts = $this->parse($record->data);
        
        $max = max(array_values($restarts));
        if ($max > self::THRESHOLD) {
            $report->setStatus(Status::warning());
        }
        
        return $report;
    }
    
    const THRESHOLD = 5;
    const REGEX = "/(\S*) - Restarts: (\d+)/m";
    
    public function parse(string $string) : array
    {
        $values = [];
        preg_match_all(self::REGEX, $string, $values);
        
        $units = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $name = $values[1][$i];
            $restarts = $values[2][$i];
            $units[$name] = $restarts;
        }
        
        return $units;
    }
}
