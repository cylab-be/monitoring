<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Server;
use App\Sensor\Linux\ArpEntry;
use App\Sensor\Linux\Arpwatch;

use Illuminate\Support\Collection;

class InsightsController extends Controller
{
    /**
     * List and search installed packages.
     *
     * @param Organization $organization
     */
    public function packages(Organization $organization)
    {
        // create an empty collection
        $packages = collect();

        foreach ($organization->servers as $device) {
            $packages = $packages->merge($this->packagesOfDevice($device));
        }

        return view("insights.packages", [
            "organization" => $organization,
            "packages" => $packages->sortBy("name")->all()]);
    }
    
    private static array $SENSORS = [
        \App\Sensor\Linux\Debian\ListPackages::class,
        \App\Sensor\FreeBSD\ListPackages::class
    ];
    
    public function packagesOfDevice(Server $device) : Collection
    {
        $packages = collect();
        
        foreach (self::$SENSORS as $sensor_class) {
            /** @var \App\Sensor $sensor */
            $sensor = new $sensor_class;
            $label = $sensor->config()->trigger_label;
            
            $last_record = $device->lastRecord($label);
            if (is_null($last_record)) {
                continue;
            }
            
            // @phpstan-ignore method.notFound
            $array = $sensor->parse($last_record->data);
            $packages = $packages->merge(
                // create a collection from array
                collect($array)
                    
                    // create a triplet [package name, device, source]
                    ->map(fn($package) => [
                        "name" => $package,
                        "device" => $device,
                "source" => $label])
            );
        }
        return $packages;
    }

    /**
     * List and search docker stacks.
     *
     * @param Organization $organization
     */
    public function stacks(Organization $organization)
    {
        // create an empty collection
        $stacks = collect();

        foreach ($organization->servers as $device) {
            $last_record = $device->lastRecord("docker_compose_stacks");
            if (is_null($last_record)) {
                continue;
            }

            $stacks = $stacks->merge(
                collect(json_decode($last_record->data))
                    ->each(fn($stack) => $stack->device = $device)
            );
        }

        return view("insights.stacks", [
            "organization" => $organization,
            "stacks" => $stacks->sortBy('Name')
        ]);
    }

    /**
     * List the network neighbors detected by arpwatch.
     *
     * @param Organization $organization
     */
    public function neighbors(Organization $organization)
    {
        // create an empty collection
        $neighbors = collect();

        // index the known devices by their addresses: ip => server
        $known = [];
        foreach ($organization->servers as $server) {
            foreach ($server->addresses() as $address) {
                $known[$address] = $server;
            }
        }

        $sensor = new Arpwatch;

        foreach ($organization->servers as $device) {
            $last_record = $device->lastRecord("arpwatch");
            if (is_null($last_record)) {
                continue;
            }

            try {
                $entries = $sensor->parse($last_record->data);
            } catch (\RuntimeException) {
                // skip devices with malformed arpwatch data
                continue;
            }

            $neighbors = $neighbors->merge(
                collect($entries)

                    // create a row [ip, mac, vendor, ...] with the owning device
                    ->map(fn(ArpEntry $entry) => [
                        "ip" => $entry->ip,
                        "mac" => $entry->mac,
                        "vendor" => $entry->vendor,
                        "name" => $entry->name,
                        "interface" => $entry->interface,
                        "seen_at" => $entry->seenAt()->format("Y-m-d H:i:s"),
                        "device" => $device,
                        "known_device" => $known[$entry->ip] ?? null,
                    ])
            );
        }

        return view("insights.neighbors", [
            "organization" => $organization,
            "neighbors" => $neighbors->sortBy(fn($n) => ip2long($n["ip"]))->all()]);
    }
}
