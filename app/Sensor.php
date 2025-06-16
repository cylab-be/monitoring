<?php

namespace App;

/**
 * Sensors must analyze a collection of Record, and if needed produce a Report.
 *
 * @author tibo
 */
abstract class Sensor
{
    abstract public function config() : SensorConfig;
    abstract public function analyze(Record $record) : ?Report;

    public function name() : string
    {
        return get_called_class();
    }

    /**
     * Compute a pseudo ID based on the full class name.
     * @return string
     */
    public function id() : string
    {
        $chunks = str_split(md5($this->name()), 8);
        return implode('-', $chunks);
    }
}
