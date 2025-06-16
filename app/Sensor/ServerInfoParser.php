<?php

namespace App\Sensor;

use App\Sensor;
use App\ServerInfo;
use App\Record;
use App\Report;

/**
 * Description of ServerInfoParser
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
