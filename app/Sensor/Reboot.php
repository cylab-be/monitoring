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
class Reboot extends \App\AbstractSensor
{
    //put your code here
    public function report() : string
    {
        return "<p>Reboot required: "
            . $this->statusHTML()
            . "</p>";
    }

    public function statusHTML()
    {
        switch ($this->status()) {
            case self::STATUS_OK:
                return "no";

            case self::STATUS_WARNING:
                return "yes";

            default:
                return "?";
        }
    }

    public function status() : int
    {
        $record = $this->getServer()->lastRecord();
        if (! isset($record['reboot'])) {
            return self::STATUS_UNKNOWN;
        }

        if ($record->reboot) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    public function getName(): string
    {
        return "Reboot required";
    }
}
