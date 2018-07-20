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
class Update extends \App\AbstractSensor {
    //put your code here
    public function report() {
        $record = $this->getLastRecord("update");
        if ($record == null) {
            return "<p>No data availabe...</p>";
        }

        return "<p>" . nl2br($record->update) . "</p>";
    }

    public function status() {
        return self::STATUS_OK;

    }

}
