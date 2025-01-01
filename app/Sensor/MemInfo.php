<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of MemInfo
 *
 * @author tibo
 */
class MemInfo implements Sensor
{
    const WARNING_RATIO = 0.9;

    public function config(): SensorConfig
    {
        return new SensorConfig("memory", "memory");
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Memory : Usage");
        $report->setHTML(view("agent.meminfo"));

        $records = $record->server->lastRecords($record->label);
        foreach ($records as $record) {
            $mem = $this->parseMeminfo($record->data);
            if ($mem->usedRatio() > self::$WARNING_RATIO) {
                return $report->setStatus(Status::warning());
            }
        }

        return $report->setStatus(Status::ok());
    }


    public function usedMemoryPoints(Collection $records)
    {
        $used = [];
        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->data);
            $used[] = new Point(
                $record->time * 1000,
                $meminfo->used() / 1000
            );
        }

        return $used;
    }

    public function cachedMemoryPoints(Collection $records)
    {
        $points = [];
        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->data);
            $points[] = new Point(
                $record->time * 1000,
                $meminfo->cached / 1000
            );
        }

        return $points;
    }

    // used = total - free - cached
    const MEMTOTAL = "/^MemTotal:\\s+([0-9]+) kB$/m";
    const MEMFREE = "/^MemFree:\\s+([0-9]+) kB$/m";
    const MEMCACHED = "/^Cached:\\s+([0-9]+) kB$/m";

    public function parseMeminfo(string $string) : Memory
    {
        return new Memory(
            $this->pregMatchOne(self::MEMTOTAL, $string),
            $this->pregMatchOne(self::MEMFREE, $string),
            $this->pregMatchOne(self::MEMCACHED, $string)
        );
    }

    public function pregMatchOne($pattern, $string)
    {
        $matches = array();
        preg_match($pattern, $string, $matches);
        return $matches[1];
    }
}
