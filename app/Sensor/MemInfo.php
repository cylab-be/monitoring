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
    const WARNING_RATIO = 0.95;

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "memory",
            "memory",
            "Parse /proc/meminfo to extract and graph memory usage"
        );
    }

    public function analyze(Record $record): Report
    {
        $server = $record->server;
        $records = $record->server->lastRecords($record->label);

        $report = (new Report())->setTitle("Memory : Usage");
        $report->setHTML(view("agent.meminfo", [
            "datasets" => [
                $this->usedMemoryPoints($records),
                $this->cachedMemoryPoints($records)
            ],
            "total_memory" => $server->info->memory / 1000
        ]));


        foreach ($records as $record) {
            $mem = $this->parseMeminfo($record->data);
            if ($mem->usedRatio() > self::WARNING_RATIO) {
                return $report->setStatus(Status::warning());
            }
        }

        return $report->setStatus(Status::ok());
    }


    public function usedMemoryPoints(Collection $records) : Dataset
    {
        $color = ColorPalette::pick1Color(0);

        $dataset = new Dataset("Used", $color);
        $dataset->backgroundColor = ColorPalette::lighten($color, 0.7);

        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->data);
            $dataset->add(new Point(
                $record->time * 1000,
                $meminfo->used() / 1000
            ));
        }

        return $dataset;
    }

    public function cachedMemoryPoints(Collection $records) : Dataset
    {
        $color = ColorPalette::pick1Color(1);

        $dataset = new Dataset("Cached", $color);
        $dataset->backgroundColor = ColorPalette::lighten($color, 0.7);

        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->data);
            $dataset->add(new Point(
                $record->time * 1000,
                $meminfo->cached / 1000
            ));
        }

        return $dataset;
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
