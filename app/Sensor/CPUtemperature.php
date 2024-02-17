<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
     * Description of Update
 *
 * @author helha
 */
class CPUtemperature implements Sensor
{
    // Match a CPU line like
    // Package id 0:  +39.0°C  (high = +84.0°C, crit = +100.0°C)
    const REGEXPCPU = "/^(Package id)+\s+(\d):\s+\+(\d+\.\d+)°C\s+\(high\s=\s\+\d+\.\d°C,\scrit\s=\s\+(\d+\.\d+)°C\)/m";
    
    // Mach a core line
    // Core 0:        +38.0°C  (high = +84.0°C, crit = +100.0°C)
    const REGEXPCORE = "/^(Core \d+):\s+\+(\d+\.\d+)°C\s+\(high\s=\s\+\d+\.\d°C,\scrit\s=\s\+(\d+\.\d+)°C\)/m";

    public function config(): SensorConfig
    {
        return new SensorConfig("cpu-temperature", "cpu-temperature");
    }
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("CPU temperature");
        
        $record = $records->last();
        $cpus = $this->parse($record->data);
        $report->setHTML(view("sensor.cputemperature", ["cpus" => $cpus]));
        $report->setStatus(Status::max($cpus));
        
        return $report;
    }

    public function parse(string $string)
    {
        $cpus = [];
        $cpu = null;
        
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $match = [];
            
            // this line corresponds to a CPU definition
            if (preg_match(self::REGEXPCPU, $line, $match) === 1) {
                $cpu = new Cpu($match[2], $match[3], $match[4]);
                $cpus[] = $cpu;
                continue;
            }
            
            // line correponds to a core definition
            if (preg_match(self::REGEXPCORE, $line, $match) === 1) {
                $core = new Core($match[1], $match[2], $match[3]);
                // append to current CPU
                $cpu->cores[] = $core;
                continue;
            }
        }
        return $cpus;
    }
}
