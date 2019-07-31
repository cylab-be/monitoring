<?php

namespace App\Sensor;

/**
 * Parse the output of netstat to list listening ports.
 *
 * @author tibo
 */
class ListeningPorts extends \App\AbstractSensor
{

    const REGEXP = "/(tcp6|tcp|udp6|udp)\s*\d\s*\d\s*(\S*):(\d*).*LISTEN\s*(\S*)/m";

    public function report()
    {
        $record = $this->getLastRecord("netstat-listen-tcp");
        if ($record == null) {
            return "<p>No data available...</p>";
        }

        $ports = $this->parse($record["netstat-listen-tcp"]);
        $return = "<table class='table table-sm'>";
        $return .= "<tr>"
                . "<th>Proto</th>"
                . "<th>Bind address</th>"
                . "<th>Port</th>"
                . "<th>Process</th>"
                . "</tr>";
        foreach ($ports as $port) {
            $return .= "<tr>"
                    . "<td>" . $port->proto . "</td>"
                    . "<td>" . $port->bind . "</td>"
                    . "<td>" . $port->port . "</td>"
                    . "<td>" . $port->process . "</td>"
                    . "</tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status()
    {
        return self::STATUS_OK;
    }

    /**
     *
     * @param string $string
     * @return \App\Sensor\ListeningPort[]
     */
    public function parse(string $string)
    {
        $values = [];
        preg_match_all(self::REGEXP, $string, $values);

        $ports = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $port = new ListeningPort();
            $port->proto = $values[1][$i];
            $port->bind = $values[2][$i];
            $port->port = $values[3][$i];
            $port->process = $values[4][$i];
            $ports[] = $port;
        }
        return $ports;
    }
}
