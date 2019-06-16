<?php

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Updates extends \App\AbstractSensor
{

    const REGEXP = "/(\d+)\spackages? can be updated\.\n(\d+)\supdates are security updates./";

    public function report()
    {
        $record = $this->getLastRecord("updates");
        if ($record == null) {
            return "<p>No data availabe...</p>";
        }

        return "<p>" . nl2br($record->updates) . "</p>";
    }

    public function status()
    {
        $record = $this->getLastRecord("updates");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }

        $status = $this->parse($record->updates);
        if ($status == null) {
            return self::STATUS_UNKNOWN;
        }

        if ($status["security"] != 0) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    public function parse($string)
    {
        $matches = [];
        if (!preg_match(self::REGEXP, $string, $matches)) {
            return null;
        }

        $result["updates"] = $matches[1];
        $result["security"] = $matches[2];
        return $result;
    }
}
