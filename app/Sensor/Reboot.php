<?php

namespace App\Sensor;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Reboot extends \App\Sensor
{
    
    public function report(array $records) : string
    {
        return "<p>Reboot required: "
            . $this->statusHTML($records)
            . "</p>";
    }

    public function statusHTML(array $records)
    {
        switch ($this->status($records)) {
            case \App\Status::OK:
                return "no";

            case \App\Status::WARNING:
                return "yes";

            default:
                return "?";
        }
    }

    public function status(array $records) : int
    {
        $record = end($records);
        if (! isset($record->data['reboot'])) {
            return \App\Status::UNKNOWN;
        }

        if ($record->data["reboot"]) {
            return \App\Status::WARNING;
        }

        return \App\Status::OK;
    }

    public function name(): string
    {
        return "Reboot required";
    }
}
