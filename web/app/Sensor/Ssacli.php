<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sensor;

/**
 * Description of Ssacli
 *
 * @author tibo
 */
class Ssacli extends \App\AbstractSensor {
    const REGEXP = "/\s*physicaldrive .*\(port (.*):box (\d*):bay (\d*), (.*), (.*), (\w*)\)/";

    public function report() {
        $record = $this->getLastRecord("ssacli");
        if ($record == null) {
            return "<p>No data available...</p>";
        }

        $disks = $this->parse($record->ssacli);
        $return = "<table class='table table-sm'>"
                . "<tr>"
                . "<th>Port</th>"
                . "<th>Box</th>"
                . "<th>Bay</th>"
                . "<th>Type</th>"
                . "<th>Size</th>"
                . "<th>Status</th>"
                . "</tr>";
        foreach ($disks as $disk) {
            $return .= "<tr>"
                    . "<td>" . $disk->port . "</td>"
                    . "<td>" . $disk->box . "</td>"
                    . "<td>" . $disk->bay . "</td>"
                    . "<td>" . $disk->type . "</td>"
                    . "<td>" . $disk->size . "</td>"
                    . "<td>" . $disk->status . "</td>"
                    . "</tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status() {
        $record = $this->getLastRecord("ssacli");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $disks = $this->parse($record->ssacli);
        foreach ($disks as $disk) {
            if ($disk->status != "OK") {
                return self::STATUS_WARNING;
            }
        }

        return self::STATUS_OK;

    }

    /**
     *
     * @param type $string
     * @return \App\Sensor\Disk[]
     */
    public function parse($string) {
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
            $disk->status = $values[6][$i];
            $disks[] = $disk;
        }
        return $disks;
    }
}


class Disk {
    public $port = "";
    public $box = 0;
    public $bay = 0;
    public $type = "";
    public $size = "";
    public $status = "";
}