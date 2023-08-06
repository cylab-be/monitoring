<?php

namespace App\Sensor;

use \App\Sensor;
use App\Record;
use App\Status;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Date extends Sensor
{
    public function report(array $records) : string
    {
        return "<p>Time drift: " . $this->delta(end($records)) . " seconds</p>";
    }

    public function status(array $records) : int
    {
        if (count($records) == 0) {
            return Status::UNKNOWN;
        }
        
        $delta = $this->delta(end($records));
        if ($delta == null) {
            return Status::UNKNOWN;
        }

        if (abs($delta) > 10) {
            return Status::WARNING;
        }

        return Status::OK;
    }

    public function delta(Record $record)
    {
        if (! isset($record->data["date"])) {
            return null;
        }

        return $record->data["date"] - $record->time;
    }
}
