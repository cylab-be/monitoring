<?php

namespace App\Sensor\Linux;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use App\Sensor\MacVendor;

/**
 * Description of Arpwatch
 *
 * @author tibo
 */
class Arpwatch extends Sensor
{
    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "arpwatch",
            "arpwatch",
            "Parse /var/lib/arpwatch/*.dat to detect (rogue ?) neighbors.",
            ["arpwatch" => "cat /var/lib/arpwatch/*.dat"]
        );
    }
    
    #[\Override]
    public function analyze(Record $record): ?Report
    {
        $neighbors = $this->parse($record->data);
        
        return (new Report)
                ->setTitle("Neighbors")
                ->setStatus(Status::ok())
                ->setHTML(blade(__DIR__ . "/Arpwatch.blade.php", ["neighbors" => $neighbors]));
    }


    /**
     * Parse an arpwatch file string into ArpEntry objects.
     *
     * Expected format per line (whitespace-separated):
     *   <mac> <ip> <unix-timestamp> <interface>
     *
     * Blank lines and lines starting with '#' (comments) are ignored.
     *
     * Returns only entries on whitelisted interfaces, vendor-enriched and
     * sorted by IP.
     *
     * @param string $data
     * @return ArpEntry[]
     * @throws \RuntimeException on any malformed non-blank line.
     */
    public function parse(string $data): array
    {
        $entries = [];
        $vendors = new MacVendor;

        // Split on any line break and trim each piece.
        $lines = preg_split('/\R/', $data) ?: [];

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);

            // Skip blanks and comments.
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Tokens separated by one or more whitespace chars.
            $parts = preg_split('/\s+/', $line) ?: [];

            if (count($parts) !== 4) {
                throw new \RuntimeException(sprintf(
                    'Malformed arp line (expected 4 fields): "%s"',
                    $rawLine
                ));
            }

            [$mac, $ip, $ts, $iface] = $parts;

            if (!preg_match('/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i', $mac)) {
                throw new \RuntimeException("Invalid MAC address: \"$mac\"");
            }
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException("Invalid IP address: \"$ip\"");
            }
            if (!ctype_digit($ts)) {
                throw new \RuntimeException("Invalid timestamp: \"$ts\"");
            }
            
            $vendor = $vendors->lookup($mac);

            $entries[] = new ArpEntry($mac, $ip, (int) $ts, $iface, $vendor);
        }
        
        // filter out irrelevant network interfaces
        $entries = array_filter(
            $entries,
            fn($neighbor) => \starts_with($neighbor->interface, Ifconfig::PREFIXES_WHITELIST)
        );
        
        // sort by IP
        usort($entries, fn($n1, $n2) => ip2long($n1->ip) <=> ip2long($n2->ip));

        return $entries;
    }
}
