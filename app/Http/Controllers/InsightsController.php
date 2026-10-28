<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Server;

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
            $packages->merge($this->packagesOfDevice($device));
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
            
            $packages = $packages->merge(
                // create a collection from array
                // @phpstan-ignore method.notFound
                collect($sensor->parse($last_record->data))
                    
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
}
