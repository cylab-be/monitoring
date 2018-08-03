<?php

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Inodes extends \App\AbstractSensor {

    const REGEXP = "/\\n([A-z\/0-9\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";

    public function report() {
        $record = $this->getLastRecord("inodes");
        if ($record == null) {
            return "<p>No data availabe...</p>";
        }

        $disks = $this->parse($record->inodes);
        $return = "<table class='table table-sm'>";
        $return .= "<tr><th></th><th></th><th>Usage</th></tr>";
        foreach ($disks as $disk) {
            $return .= "<tr><td>" . $disk->filesystem . "</td><td>" . $disk->mounted . "</td><td>" . $disk->usedPercent() . "%</td></tr>";
        }
        $return .= "</table>";
        return $return;

    }

    public function status() {
        $record = $this->getLastRecord("inodes");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $all_status = [];
        foreach ($this->parse($record->inodes) as $disk) {
            /* @var $disk DiskInodes */
            $status = self::STATUS_OK;
            if ($disk->usedPercent() > 80) {
                $status = self::STATUS_WARNING;
            } elseif ($disk->usedPercent() > 95) {
                $status = self::STATUS_ERROR;
            }
            $all_status[] = $status;
        }

        return max($all_status);
    }

    public function parse($string) {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $disks = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $disk = new DiskInodes();
            $disk->filesystem = $values[1][$i];
            $disk->inodes = $values[2][$i];
            $disk->used = $values[3][$i];
            $disk->mounted = $values[6][$i];
            $disks[] = $disk;
        }
        return $disks;
    }
}

class DiskInodes {
    public $filesystem = "";
    public $inodes = 0;
    public $used = 0;
    public $mounted = "";

    public function usedPercent() {
        return round(100.0 * $this->used / $this->inodes);
    }
}
