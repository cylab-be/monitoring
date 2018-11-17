<?php

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Disks extends \App\AbstractSensor {

    const REGEXP = "/\\n([A-z\/0-9:\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";

    public function report() {
        $record = $this->getLastRecord("disks");
        if ($record == null) {
            return "<p>No data available...</p>";
        }

        $partitions = $this->parse($record->disks);
        $return = "<table class='table table-sm'>";
        $return .= "<tr><th></th><th></th><th>Usage</th></tr>";
        foreach ($partitions as $partition) {
            $return .= "<tr><td>" . $partition->filesystem . "</td><td>" . $partition->mounted . "</td><td>" . $partition->usedPercent() . "%</td></tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status() {
        $record = $this->getLastRecord("disks");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $all_status = [];
        foreach ($this->parse($record->disks) as $partition) {
            /* @var $partition Partition */
            $status = self::STATUS_OK;
            if ($partition->usedPercent() > 80) {
                $status = self::STATUS_WARNING;
            } elseif ($partition->usedPercent() > 95) {
                $status = self::STATUS_ERROR;
            }
            $all_status[] = $status;
        }

        return max($all_status);
    }

    public static $skip_fs = ["none", "tmpfs", "shm"];

    public function parse($string) {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $partitions = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $fs = $values[1][$i];
            if (in_array($fs, self::$skip_fs)) {
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
}

class Partition {
    public $filesystem = "";
    public $blocks = 0;
    public $used = 0;
    public $mounted = "";

    public function usedPercent() {
        return round(100.0 * $this->used / $this->blocks);
    }
}
