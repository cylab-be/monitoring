<?php

namespace App\Sensor;

use App\Sensor;
use App\ServerInfo;
use App\Record;
use App\Report;

/**
 * A special kind of sensor that produces no report, but will update general information about the device
 * E.g. CPU name, CPU core count, total memory etc.
 *
 * @author tibo
 */
abstract class ServerInfoParser extends Sensor
{
    public function analyze(Record $record): ?Report
    {
        $info = $record->server->info;
        $string = $record->data;
        
        $this->analyzeString($string, $info);
        $info->save();
        return null;
    }
    
    abstract public function analyzeString(string $string, ServerInfo $info);
}
