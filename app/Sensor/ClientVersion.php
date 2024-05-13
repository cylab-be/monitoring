<?php

namespace App\Sensor;

use App\Jobs\FetchClientManifest;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

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
    
    public function analyze(Record $record): Report
    {
        $latest_version = FetchClientManifest::version();
        $installed_version = $record->data;
        
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
}
