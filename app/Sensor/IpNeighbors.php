<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
 * Description of IpNeighbors
 *
 * @author tibo
 */
class IpNeighbors extends Sensor
{
    public function analyze(Record $record): ?Report
    {
        $html = $this->parse($record->data);
        
        return (new Report)
                ->setTitle("Neighbors")
                ->setStatus(Status::ok())
                ->setHTML($html);
    }
    
    public function parse(string $data)
    {
        $neighbors = json_decode($data);
        $neighbors = array_filter(
            $neighbors,
            fn($neighbor) => \starts_with($neighbor->dev, Ifconfig::PREFIXES_WHITELIST)
        );
        
        usort(
            $neighbors,
            fn($n1, $n2) => ip2long($n1->dst) > ip2long($n2->dst) ? 1 : -1
        );
        
        return view("sensor.ipneighbors", ["neighbors" => $neighbors])->render();
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ip-neighbors",
            "ip-neighbors",
            "Parse ip neighbors to detect (rogue ?) neighbors"
        );
    }
}
