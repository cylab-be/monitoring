<?php

namespace App\Sensor;

use App\Record;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Date extends \App\AbstractSensor
{
    public function report(array $records) : string
    {
        return "<p>Time drift: " . $this->delta(end($records)) . " seconds</p>";
    }

    public function status(array $records) : int
    {
        $delta = $this->delta(end($records));
        if ($delta == null) {
            return \App\Status::UNKNOWN;
        }

        if (abs($delta) > 10) {
            return \App\Status::WARNING;
        }

        return \App\Status::OK;
    }

    public function delta(Record $record)
    {
        if (! isset($record->data["date"])) {
            return null;
        }

        return $record->data["date"] - $record->time;
    }
}
