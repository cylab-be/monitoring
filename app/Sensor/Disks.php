<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Report;
use App\Status;
use App\Record;

use Illuminate\Support\Collection;

/**
 * Monitor disk usage
 *
 * @author tibo
 */
class Disks implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "disks",
            "disks",
            "Uses df command to check storage partitions usage"
        );
    }


    const REGEXP = "/\\n([A-z\/0-9:\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Storage : Partitions");

        $partitions = $this->parse($record->data);
        $report->setHTML(view("sensor.disks", ["partitions" => $partitions]));

        return $report->setStatus(Status::max($partitions));
    }

    /**
     *
     * @param string $string
     * @return Collection<Partition>
     */
    public function parse(string $string) : Collection
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);


        $partitions = new Collection();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $fs = $values[1][$i];
            if ($this->shouldSkip($fs)) {
                continue;
            }

            $partition = new Partition();
            $partition->filesystem = $fs;
            $partition->blocks = $values[2][$i];
            $partition->used = $values[3][$i];
            $partition->mounted = $values[6][$i];
            $partitions->push($partition);
        }
        return $partitions;
    }

    public function fromRecord($record) : Collection
    {
        $partitions = $this->parse($record->data);
        $time = $record->time;
        foreach ($partitions as $partition) {
            $partition->time = $time;
        }

        return $partitions;
    }

    const SKIP_FS = ["none", "tmpfs", "shm", "udev", "overlay", '/dev/loop', "devfs"];

    public function shouldSkip(string $fs) : bool
    {
        foreach (self::SKIP_FS as $should_skip) {
            if ($this->startsWith($should_skip, $fs)) {
                return true;
            }
        }

        return false;
    }

    public function startsWith(string $needle, string $haystack) : bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
