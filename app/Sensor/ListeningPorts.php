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
        $tcp_record = $this->getLastRecord("netstat-listen-tcp");
        $udp_record = $this->getLastRecord("netstat-listen-udp");
        if ($tcp_record == null && $udp_record == null) {
            return "<p>No data available...</p>";
        }

        $ports = array_merge(
                $this->parse($tcp_record["netstat-listen-tcp"]),
                $this->parse($udp_record["netstat-listen-udp"]));

        usort($ports,
                function(ListeningPort $port1, ListeningPort $port2) {
                    return $port1->port - $port2->port;
                });

        $return = "<table class='table table-sm'>";
        $return .= "<tr>"
                . "<th>Port</th>"
                . "<th>Proto</th>"
                . "<th>Bind address</th>"
                . "<th>Process</th>"
                . "</tr>";
        foreach ($ports as $port) {
            $return .= "<tr>"
                    . "<td>" . $port->port . "</td>"
                    . "<td>" . $port->proto . "</td>"
                    . "<td>" . $port->bind . "</td>"
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
