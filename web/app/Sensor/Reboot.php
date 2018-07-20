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
class Reboot extends \App\AbstractSensor {
    //put your code here
    public function report() {
        return "<p>Reboot required: "
            . $this->statusHTML()
            . "</p>";
    }

    function statusHTML() {
        switch ($this->status()) {
            case self::STATUS_OK :
                return "no";

            case self::STATUS_WARNING :
                return "yes";

            default:
                return "?";
        }
    }

    public function status() {
        $record = $this->getLastRecord("reboot");
        if ($record === null) {
            return self::STATUS_UNKNOWN;
        }


        if ($record->reboot) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

}
