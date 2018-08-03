<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sensor;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Date extends \App\AbstractSensor {
    //put your code here
    public function report() {
        return "<p>Time drift: " . $this->delta() . " seconds</p>";
    }



    public function status() {
        $delta = $this->delta();
        if ($delta == null) {
            return self::STATUS_UNKNOWN;
        }

        if (abs($delta) > 10) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    public function delta() {
        $record = $this->getLastRecord("date");
        if ($record === null) {
            return null;
        }

        return $record->date - $record->time;
    }

}
