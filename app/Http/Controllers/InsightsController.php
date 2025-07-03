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
}
