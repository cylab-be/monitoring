<?php

namespace App\Sensor;

use App\Sensor;
use App\Jobs\FetchClientManifest;

/**
 * Check if the latest version of the client is installed.
 *
 * @author tibo
 */
class ClientVersion extends Sensor
{
    public function report(array $records) : string
    {
        return "<p>Installed version: " . $this->installedVersion($records) . "</p>"
        . "<p>Latest client version: " . FetchClientManifest::version() . "</p>";
    }

    public function installedVersion(array $records)
    {
        $last_record = end($records);
        if ($last_record == null) {
            return "none";
        }

        return $last_record->data["version"];
    }

    public function status(array $records) : int
    {
        $latest_version = FetchClientManifest::version();
        if ($latest_version == null) {
            return \App\Status::UNKNOWN;
        }

        if ($this->installedVersion($records) === $latest_version) {
            return \App\Status::OK;
        }

        return \App\Status::WARNING;
    }
}
