<?php

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Updates extends \App\AbstractSensor
{

    const REGEXP = "/(\d+)\spackages? can be updated\.\n(\d+)\supdates? (is a|are) security updates?./";

    public function report(array $records) : string
    {
        $record = end($records);
        if (! isset($record['updates'])) {
            return "<p>No data available...</p>";
        }

        return "<p>" . nl2br($record->updates) . "</p>";
    }

    public function status() : int
    {
        $record = $this->getServer()->lastRecord();
        if (! isset($record['updates'])) {
            self::STATUS_UNKNOWN;
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
