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
class Heartbeat extends \App\AbstractSensor
{
    //put your code here
    public function report()
    {
        return "<p>Last heartbeat received "
        . $this->getServer()->lastRecordTime()->diffForHumans() . "</p>";
    }


    public function status()
    {
        $record = $this->getServer()->lastRecord();

        if ($record === null) {
            $delta = PHP_INT_MAX;
        } else {
            $delta = \time() - $record->time;
        }

        if ($delta > 900) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }
}
