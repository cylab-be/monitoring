<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Record;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of MemInfo
 *
 * @author tibo
 */
class Ifconfig implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig("ifconfig", "ifconfig");
    }
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("Ifconfig");
        
        $last_record = $records->last();
        $interfaces = $this->parseIfconfigRecord($last_record);
        return $report->setStatus(Status::ok())
                ->setHTML(view("agent.ifconfig", ["interfaces" => $interfaces]));
    }

    public function points(Collection $records)
    {
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

            $dataset[$iname . "/RX"] = [
                "name" => $iname . "/RX",
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
                if ($delta < 0) {
                    // Can happen after a reboot...
                    $delta = 0;
                }
                $dataset[$iname . "/RX"]["points"][] = new Point(
                    $interface->time * 1000,
                    round(8 / 1024 * $delta / $delta_time)
                );

                // TX
                $delta = $interface->tx - $previous_value->tx;
                if ($delta < 0) {
                    // Can happen after a reboot...
                    $delta = 0;
                }
                $dataset[$iname . "/TX"]["points"][] = new Point(
                    $interface->time * 1000,
                    round(8 / 1024 * $delta / $delta_time)
                );

                // Keep current value for next record
                $current_value[$iname] = $interface;
            }
        }

        return array_values($dataset);
    }

    const IFNAME = '/^(?|(\S+)\s+Link encap:|(\S+): flags)/m';
    const IPV4 = '/^\s+inet (?>addr:)?(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/m';
    const RXTX = '/^\s+RX bytes:(\d+) .*TX bytes:(\d+)/m';
    const RX = '/^\s+RX packets (?>\d+)  bytes (\d+)/m';
    const TX = '/^\s+TX packets (?>\d+)  bytes (\d+)/m';

    public function parseIfconfigRecord(Record $record)
    {
        $interfaces = $this->parseIfconfig($record->data);
        foreach ($interfaces as $interface) {
            $interface->time = $record->time;
        }

        return $interfaces;
    }

    /**
     * Parse the result of the ifconfig command, skipping every virtual
     * interfaces (docker, br, lo) and return an array of NetworkInterface
     * @param string $string
     * @return \App\Sensor\NetworkInterface[]
     */
    public function parseIfconfig(string $string) : array
    {
        $allowed_prefixes = ["en", "eth", "wl", "venet", "igb", "ax", "tun"];

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

            $matches = [];
            if (preg_match(self::RXTX, $line, $matches) === 1) {
                $if->rx = $matches[1];
                $if->tx = $matches[2];
                continue;
            }

            $rx = $this->pregMatchOne(self::RX, $line);
            if ($rx !== false) {
                $if->rx = $rx;
            }

            $tx = $this->pregMatchOne(self::TX, $line);
            if ($tx !== false) {
                $if->tx = $tx;
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

    public function pregMatchOne(string $pattern, string $string, int $match_group = 1)
    {
        $matches = [];
        if (preg_match($pattern, $string, $matches) === 1) {
            return $matches[$match_group];
        }

        return false;
    }
}
