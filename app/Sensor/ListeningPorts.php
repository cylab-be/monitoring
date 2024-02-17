<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Parse the output of netstat to list listening ports.
 *
 * @author tibo
 */
class ListeningPorts implements Sensor
{
    
    public function config(): SensorConfig 
    {
        return new SensorConfig("listening-tcp", "netstat-listen-tcp");
    }

    const REGEXP = "/(tcp6|tcp|udp6|udp)\s*\d\s*\d\s*(\S*):(\d*).*LISTEN\s*(\S*)/m";
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("Listening Ports");
        $record = $records->last();

        $ports = $this->parse($record->data);
        usort(
            $ports,
            function (ListeningPort $port1, ListeningPort $port2) {
                    return $port1->port - $port2->port;
            }
        );
        
        return $report->setStatus(Status::ok())
                ->setHTML(view("sensor.listeningports", ["ports" => $ports]));
    }

    /**
     *
     * @param string $string
     * @return \App\Sensor\ListeningPort[]
     */
    public function parse(?string $string)
    {
        if ($string == null) {
            return [];
        }

        $values = [];
        preg_match_all(self::REGEXP, $string, $values);

        $ports = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            // bind address
            $bind = trim($values[2][$i]);
            if ($bind == "127.0.0.1" || $bind == "::1") {
                continue;
            }
            
            $port = new ListeningPort();
            $port->proto = $values[1][$i];
            $port->bind = $bind;
            $port->port = $values[3][$i];
            $port->process = $values[4][$i];
            $ports[] = $port;
        }
        return array_unique($ports);
    }
}
