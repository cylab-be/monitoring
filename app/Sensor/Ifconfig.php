<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Illuminate\Database\Eloquent\Collection;

/**
 * Parse ifconfig command to get IP addresses and traffic
 *
 * @author tibo
 */
class Ifconfig extends Sensor
{
    /**
     * List of interface name prefixes.
     */
    const PREFIXES_WHITELIST = [
        "eno", "ens", "enp", "eth", "wl", "venet", "igb", "ax", "tun",
        "bge",
        "ovpn", // OpenVPN (OpnSENSE, FreeBSD)
        "wg", // Wireguard (OpnSENSE, FreeBSD)
        "igc", "ixl",
        "ppp"]; // E.g. pppoe0, used for Internet connections

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "ifconfig",
            "ifconfig",
            "Use ifconfig command to graph network traffic"
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Network : Traffic");

        $server = $record->server;
        $interfaces = $this->parseIfconfigRecord($record);
        return $report->setStatus(Status::ok())
                ->setHTML(view("agent.ifconfig", [
                    "interfaces" => $interfaces,
                    "points" => $this->points($server->lastRecords("ifconfig"))]));
    }

    /**
     * Compute datasets (array time ordered points) that can be fed to Chart.js
     *
     * @param Collection $records
     * @return array
     */
    public function points(Collection $records) : array
    {
        // Compute the time ordered list of interfaces
        $interfaces = [];
        foreach ($records as $record) {
            $interfaces[] = $this->parseIfconfigRecord($record);
        }

        // Foreach interface, compute the array of points
        $datasets = [];
        $current_value = [];
        foreach ($interfaces[0] as $key => $interface) {
            $colors = ColorPalette::pick2Colors($key);

            $iname = $interface->name;
            $datasets[$iname . "/TX"] = new Dataset($iname . "/TX", $colors[0]);
            $datasets[$iname . "/RX"] = new Dataset($iname . "/RX", $colors[1]);

            $current_value[$interface->name] = $interface;
        }

        for ($i = 1; $i < count($interfaces); $i++) {
            foreach ($interfaces[$i] as $interface) {
                $iname = $interface->name;

                // in some cases (like VPN), the interface will be turned on and off
                // so the current value will be sometimes available and sometimes not
                if (! isset($current_value[$iname])) {
                    continue;
                }

                $previous_value = $current_value[$iname];
                $delta_time = $interface->time - $previous_value->time;

                // RX
                $delta = $interface->rx - $previous_value->rx;
                if ($delta < 0) {
                    // Can happen after a reboot...
                    $delta = 0;
                }
                $datasets[$iname . "/RX"]->add(new Point(
                    $interface->time * 1000,
                    round(8 / 1024 * $delta / $delta_time)
                ));

                // TX
                $delta = $interface->tx - $previous_value->tx;
                if ($delta < 0) {
                    // Can happen after a reboot...
                    $delta = 0;
                }
                $datasets[$iname . "/TX"]->add(new Point(
                    $interface->time * 1000,
                    round(8 / 1024 * $delta / $delta_time)
                ));

                // Keep current value for next record
                $current_value[$iname] = $interface;
            }
        }

        return array_values($datasets);
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
                $if->addresses[] = $ip;
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
            if (\starts_with($interface->name, self::PREFIXES_WHITELIST)) {
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
