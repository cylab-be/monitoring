<?php

namespace App\Sensor;

use App\Sensor;
use App\ServerInfo;
use App\Report;
use App\Status;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Update
 *
 * @author tibo
 */
class Disks implements Sensor
{

    const REGEXP = "/\\n([A-z\/0-9:\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("Partitions");
        
        $record = $records->last();
        if (! isset($record->data['disks'])) {
            return $report->setHTML("<p>No data available...</p>");
        }

        $partitions = $this->parse($record->data["disks"]);
        $report->setHTML(view("sensor.disks", ["partitions" => $partitions]));
        
        return $report->setStatus(Status::max($partitions));
    }

    public function parse(string $string) : array
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $partitions = array();
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
            $partitions[] = $partition;
        }
        return $partitions;
    }

    public function fromRecord($record) : array
    {
        $partitions = $this->parse($record->data["disks"]);
        $time = $record->time;
        foreach ($partitions as $partition) {
            $partition->time = $time;
        }

        return $partitions;
    }
    
    const SKIP_FS = ["none", "tmpfs", "shm", "udev", "overlay", '/dev/loop'];
    
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
