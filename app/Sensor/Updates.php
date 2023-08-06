<?php

namespace App\Sensor;

/**
 * Description of Update
 *
 * @author tibo
 */
class Updates extends \App\Sensor
{

    const REGEXP = "/(\d+)\spackages? can be updated\.\n(\d+)\supdates? (is a|are) security updates?./";

    public function report(array $records) : string
    {
        $record = end($records);
        if (! isset($record->data['updates'])) {
            return "<p>No data available...</p>";
        }

        return "<p>" . nl2br($record->data["updates"]) . "</p>";
    }

    public function status(array $records) : int
    {
        $record = end($records);
        if (! isset($record->data['updates'])) {
            return \App\Status::UNKNOWN;
        }

        $status = $this->parse($record->data["updates"]);
        if ($status == null) {
            return \App\Status::UNKNOWN;
        }

        if ($status["security"] != 0) {
            return \App\Status::WARNING;
        }

        return \App\Status::OK;
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
