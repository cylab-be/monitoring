<?php

namespace App\Sensor\FreeBSD;

use App\Record;
use App\Sensor;
use App\SensorConfig;
use App\Report;
use App\Status;

/**
 * Description of OpnSenseCheckUpdate
 *
 * @author tibo
 */
class OpnSenseCheckUpdate extends Sensor
{
    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "opnsense-check-update",
            "opnsense-check-update",
            "Parse the output of /usr/local/opnsense/scripts/firmware/check.sh to check if updates are available",
            ["opnsense-check-update" => "/usr/local/opnsense/scripts/firmware/check.sh"]
        );
    }
    
    //put your code here
    #[\Override]
    public function analyze(Record $record): ?Report
    {
        $updates = $this->parse($record->data);
        $report = (new Report())
                ->setTitle("OpnSENSE Updates")
                ->setStatus(Status::ok());
        
        if ($updates["install"] > 0 || $updates["upgrade"] > 0) {
            $report->setStatus(Status::warning());
        }
        
        $report->setHTML(blade(__DIR__ . "/OpnSenseCheckUpdate.blade.php", ["updates" => $updates]));
        
        return $report;
    }

    public function parse(string $data) : array
    {
        $results = [
            'install' => 0,
            'upgrade' => 0,
        ];

        // Regex pattern to find "Number of packages to be installed: X"
        // \s* handles any unexpected whitespace
        // (\d+) captures the digits into a group
        $installPattern = '/Number of packages to be installed:\s*(\d+)/';

        // Regex pattern to find "Number of packages to be upgraded: X"
        $upgradePattern = '/Number of packages to be upgraded:\s*(\d+)/';

        if (preg_match($installPattern, $data, $installMatches)) {
            $results['install'] = (int) $installMatches[1];
        }

        if (preg_match($upgradePattern, $data, $upgradeMatches)) {
            $results['upgrade'] = (int) $upgradeMatches[1];
        }

        return $results;
    }
}
