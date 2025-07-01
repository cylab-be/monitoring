<?php

namespace App\Sensor;

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
class ClientVersion extends Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "client_version",
            "version",
            "Check that the latest version of monitoring client is installed"
        );
    }

    public function analyze(Record $record): Report
    {
        $latest_version = $this->currentVersion();
        $installed_version = $record->data;

        $report = (new Report())->setTitle("Client Version");
        $report->setHTML(
            "<p>Installed version: $installed_version</p>" .
            "<p>Latest client version: $latest_version</p>"
        );

        if ($installed_version === $latest_version) {
            $report->setStatus(Status::ok());
        } else {
            $report->setStatus(Status::warning());
        }

        return $report;
    }

    /**
     * Extract current version number from client script.
     * @return string
     */
    public function currentVersion() : string
    {
        $regex = '/^\$VERSION = "(\d+\.\d+\.\d+)";$/m';
        $client = file_get_contents(__DIR__ . "/../../public/monitor");
        $version = preg_match_one($regex, $client);

        return $version;
    }
}
