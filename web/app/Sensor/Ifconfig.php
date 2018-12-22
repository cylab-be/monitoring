<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of MemInfo
 *
 * @author tibo
 */
class Ifconfig extends AbstractSensor {

    public function report() {

        $interfaces = [];
        $record = $this->getLastRecord("ifconfig");
        if ($record !== null) {
            $interfaces = $this->parseIfconfigRecord($record);
        }
        return view("agent.ifconfig", [
            "server" => $this->getServer(),
            "interfaces" => $interfaces]);
    }

    public function points() {
        // Get records in time ascending order
        $records = $this->getLastRecords("ifconfig", 289);
        usort($records, function($r1, $r2) {
            return $r1->time  > $r2->time ? 1 : -1;
        });

        // Compute the time ordered list of arrays of interfaces
        $interfaces = [];
        foreach ($records as $record) {
            $interfaces[] = $this->parseIfconfigRecord($record);
        }

        // Foreach interface, compute the array of points
        $dataset = [];
        $current_value = [];
        foreach ($interfaces[0] as $interface) {
            $iname = $interface->name;
            $dataset[$iname . "/TX"] = [
                "name" => $iname . "/TX",
                "points" => []
            ];
            $dataset[$iname . "/TX"] = [
                "name" => $iname . "/TX",
                "points" => []
            ];
            $current_value[$interface->name] = $interface;
        }

        for ($i = 1; $i < count($interfaces); $i++) {
            foreach ($interfaces[$i] as $interface) {
                $iname = $interface->name;
                $previous_value = $current_value[$iname];
                $delta_time = $interface->time - $previous_value->time;

                // RX
                $delta = $interface->rx - $previous_value->rx;
                $dataset[$iname . "/RX"]["points"][] = new Point(
                        $interface->time * 1000,
                        $delta / $delta_time);

                // TX
                $delta = $interface->tx - $previous_value->tx;
                $dataset[$iname . "/TX"]["points"][] = new Point(
                        $interface->time * 1000,
                        $delta / $delta_time);

                // Keep current value for next record
                $current_value[$iname] = $interface;

            }
        }

        return array_values($dataset);

    }

    public function status() {
        return self::STATUS_OK;
    }

    const IFNAME = "/^(\\S+)\\s+Link encap:/m";
    const IPV4 = '/^\\s+inet addr:(\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3})/m';
    const RXTX = '/^\\s+RX bytes:(\\d+) .*TX bytes:(\\d+)/m';

    public function parseIfconfigRecord($record) {
        $interfaces = $this->parseIfconfig($record->ifconfig);
        foreach ($interfaces as $interface) {
            $interface->time = $record->time;
        }

        return $interfaces;
    }

    /**
     * Parse the result of the ifconfig command.
     * @param type $string
     * @return \App\Sensor\NetworkInterface[]
     */
    public function parseIfconfig($string) {

        $allowed_prefixes = ["en", "eth"];

        if ($string == null) {
            return [];
        }

        $interfaces = [];
        $lines = explode("\n", $string);
        $if = null;
        foreach ($lines as $line) {
            $name = $this->pregMatchOne(self::IFNAME, $line);
            if ($name !== false) {
                // Starting the section of a new interface
                $if = new NetworkInterface();
                $interfaces[] = $if;
                $if->name = $name;
                continue;
            }

            $ip = $this->pregMatchOne(self::IPV4, $line);
            if ($ip !== false) {
                $if->address = $ip;
                continue;
            }

            $matches = array();
            if (preg_match(self::RXTX, $line, $matches) === 1) {
                $if->rx = $matches[1];
                $if->tx = $matches[2];
                continue;
            }
        }

        // filter out uninteresting interfaces
        $filtered = [];
        foreach ($interfaces as $interface) {
            if (\starts_with($interface->name, $allowed_prefixes)) {
                $filtered[] = $interface;
            }
        }

        return $filtered;
    }

    public function pregMatchOne($pattern, $string) {
        $matches = array();
        if (preg_match($pattern, $string, $matches) === 1) {
            return $matches[1];
        }

        return false;
    }
}

class NetworkInterface {
    public $name;
    public $address;
    public $rx;
    public $tx;
    public $time;

    public function humanReadableSize($bytes, $decimals = 2) {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public function humanReadableRx() {
        return $this->humanReadableSize($this->rx);
    }

    public function humanReadableTx() {
        return $this->humanReadableSize($this->tx);
    }

}
