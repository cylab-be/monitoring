<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
 * Description of Ssacli
 *
 * @author tibo
 */
class Ssacli implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
                "ssacli",
                "ssacli",
                "Parse ssacli on HP RAID controllers to check the status of physical disks");
    }

    const REGEXP = "/\s*physicaldrive .*\(port (.*):box (\d*):bay (\d*), (.*), (.*), (\w*)\)/";

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Storage : HP ssacli");

        $disks = $this->parse($record->data);
        $report->setHTML(view("sensor.ssacli", ["disks" => $disks]));

        return $report->setStatus(Status::max($disks));
    }

    /**
     *
     * @param string $string
     * @return \App\Sensor\Disk[]
     */
    public function parse($string)
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $disks = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $disk = new Disk();
            $disk->port = $values[1][$i];
            $disk->box = $values[2][$i];
            $disk->bay = $values[3][$i];
            $disk->type = $values[4][$i];
            $disk->size = $values[5][$i];
            $disk->status = ($values[6][$i] == "OK") ? Status::ok() : Status::warning();
            $disks[] = $disk;
        }
        return $disks;
    }
}
