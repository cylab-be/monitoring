<?php

namespace App\Sensor;

use App\Jobs\FetchClientManifest;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Check if the latest version of the client is installed.
 *
 * @author tibo
 */
class ClientVersion implements Sensor
{
    public function config(): SensorConfig 
    {
        return new SensorConfig("client-version", "version");
    }
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $latest_version = FetchClientManifest::version();
        $installed_version = $this->installedVersion($records);
        
        $report = (new Report())->setTitle("Client Version");
        $report->setHTML(
            "<p>Installed version: $installed_version</p>" .
            "<p>Latest client version: $latest_version</p>"
        );
        
        if ($latest_version == null) {
            $report->setStatus(Status::unknown());
        } elseif ($installed_version === $latest_version) {
            $report->setStatus(Status::ok());
        } else {
            $report->setStatus(Status::warning());
        }

        return $report;
    }
    
    public function installedVersion(Collection $records) : string
    {
        $last_record = $records->last();
        if ($last_record == null) {
            return "none";
        }

        return $last_record->data;
    }
}
