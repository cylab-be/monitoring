<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of MemInfo
 *
 * @author tibo
 */
class MemInfo extends AbstractSensor
{

    public function report()
    {

        return view("agent.meminfo", ["server" => $this->getServer()]);
    }

    public function usedMemoryPoints()
    {
        $records = $this->getLastRecords("memory", 288);

        $used = [];
        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->memory);
            $used[] = new Point(
                $record->time * 1000,
                $meminfo->used() / 1000
            );
        }

        return $used;
    }

    public function cachedMemoryPoints()
    {
        $records = $this->getLastRecords("memory", 288);

        $points = [];
        foreach ($records as $record) {
            $meminfo = $this->parseMeminfo($record->memory);
            $points[] = new Point(
                $record->time * 1000,
                $meminfo->cached / 1000
            );
        }

        return $points;
    }

    public function status()
    {
        return self::STATUS_OK;
    }

    // used = total - free - cached
    const MEMTOTAL = "/^MemTotal:\\s+([0-9]+) kB$/m";
    const MEMFREE = "/^MemFree:\\s+([0-9]+) kB$/m";
    const MEMCACHED = "/^Cached:\\s+([0-9]+) kB$/m";

    public function parseMeminfo($string)
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
