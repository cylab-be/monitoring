<?php

namespace App;

/**
 * Sensors must analyze a collection of Record, and if needed produce a Report.
 *
 * @author tibo
 */
interface Sensor
{
    public function config() : SensorConfig;
    public function analyze(Record $record) : ?Report;
}
