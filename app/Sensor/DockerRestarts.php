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
        // first record from 24h ago
        $server = $record->server;
        $record24 = $server->records()
                ->where("label", "docker-restarts")
                ->where("time", ">", time() - 24 * 3600)
                ->first();
        
        if (is_null($record24)) {
            throw new \Exception("No record found for comparison ...");
        }
        
        $report = (new Report)->setTitle("Docker restart");
        $report->setStatus(Status::ok());
        
        $restarts = $this->parse($record->data);
        $restarts24 = $this->parse($record24->data);
        $delta = $this->delta($restarts, $restarts24);
        
        $report->setHTML(view("sensor.dockerrestart", ["restarts" => $delta]));
        
        $max = max(array_values($delta));
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
    
    public function delta(array $now, array $earlier) : array
    {
        $delta = [];
        foreach ($now as $container => $restarts_now) {
            if (! isset($earlier[$container])) {
                $delta[$container] = $restarts_now;
            } else {
                // sometime delta may be negative, for example after a server reboot
                // in that case we zeroize
                $delta[$container] = max(
                    $restarts_now - $earlier[$container],
                    0
                );
            }
        }
        
        return $delta;
    }
}
