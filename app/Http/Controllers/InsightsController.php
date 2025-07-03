<?php

namespace App\Http\Controllers;

use App\Organization;

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
            $last_record = $device->lastRecord("apt_list");
            if (is_null($last_record)) {
                continue;
            }

            $packages = $packages->merge(
                // create collection
                collect(explode("\n", $last_record->data))

                    // first line simply contains 'Listing...'
                    ->skip(1)

                    // create a triplet [package name, device, source]
                    ->map(fn($package) => [
                        "name" => $package,
                        "device" => $device,
                "source" => "apt_list"])
            );
        }

        return view("insights.packages", [
            "organization" => $organization,
            "packages" => $packages->sortBy("name")->all()]);
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
