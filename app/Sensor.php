<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

/**
 * Sensors must analyze a collection of Record, and produce a Report.
 *
 * @author tibo
 */
interface Sensor
{
    public function config() : SensorConfig;
    public function analyze(Collection $records, ServerInfo $serverinfo) : Report;
}
