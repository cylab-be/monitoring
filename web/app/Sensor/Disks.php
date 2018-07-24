<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Disks extends \App\AbstractSensor {

    const REGEXP = "/\\n([A-z\/0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";

    public function report() {
        $record = $this->getLastRecord("disks");
        if ($record == null) {
            return "<p>No data availabe...</p>";
        }

        $disks = $this->parse($record->disks);
        $return = "<table class='table table-sm'>";
        $return .= "<tr><th></th><th>Usage</th></tr>";
        foreach ($disks as $disk) {
            $return .= "<tr><td>" . $disk->filesystem . "</td><td>" . $disk->usedPercent() . "%</td></tr>";
        }
        $return .= "</table>";
        return $return;

    }

    public function status() {
        return self::STATUS_OK;

    }

    public function parse($string) {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $disks = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $disk = new Disk();
            $disk->filesystem = $values[1][$i];
            $disk->blocks = $values[2][$i];
            $disk->used = $values[3][$i];
            $disk->mounted = $values[6][$i];
            $disks[] = $disk;
        }
        return $disks;
    }
}

class Disk {
    public $filesystem = "";
    public $blocks = 0;
    public $used = 0;
    public $mounted = "";

    public function usedPercent() {
        return round(100.0 * $this->used / $this->blocks);
    }
}
