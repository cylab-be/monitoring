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
class Updates extends \App\AbstractSensor {

    const REGEXP = "/(\d+)\spackages can be updated\.\n(\d+)\supdates are security updates./";

    public function report() {
        $record = $this->getLastRecord("updates");
        if ($record == null) {
            return "<p>No data availabe...</p>";
        }

        return "<p>" . nl2br($record->updates) . "</p>";
    }

    public function status() {
        $record = $this->getLastRecord("updates");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $status = $this->parse($record->updates);
        if ($status["security"] != 0) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    public function parse($string) {
        $matches = [];
        preg_match(self::REGEXP, $string, $matches);
        $result["updates"] = $matches[1];
        $result["security"] = $matches[2];
        return $result;
    }

}
