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
class SystemdFailedUnits extends Sensor
{
    #[\Override]
    public function analyze(Record $record): ?\App\Report
    {
        $report = (new Report)->setTitle("Failed systemd units");
        $report->setStatus(Status::ok());
        $report->setHTML("<pre>" . $record->data . "</pre>");
        
        $units = $this->parse($record->data);
        if (count($units) > 0) {
            $report->setStatus(Status::warning());
        }
        
        return $report;
    }

    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "systemd-failed-units",
            "systemd-failed-units",
            "Parse systemctl list-units to detect failed units"
        );
    }
    
    const REGEX = "/^● (\S*) \S+ failed/m";
    
    public function parse(string $string) : array
    {
        $values = [];
        preg_match_all(self::REGEX, $string, $values);
        
        $units = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $units[] = $values[1][$i];
        }
        
        // var_dump($values);
        
        return $units;
    }
}
