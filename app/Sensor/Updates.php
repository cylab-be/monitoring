<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
 * Check if (security) updates are available.
 *
 * @author tibo
 */
class Updates implements Sensor
{
    
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "updates",
            "updates",
            "Parse /var/lib/update-notifier/updates-available to check if updates are available"
        );
    }

    const REGEXP = "/(\d+)\spackages? can be updated\.\n(\d+)\supdates? (is a|are) security updates?./";
    
    public function analyze(Record $record): Report
    {
        $report = (new Report())
                ->setTitle("Updates available")
                ->setHTML("<p>" . nl2br($record->data) . "</p>");
        
        $status = $this->parse($record->data);
        if ($status == null) {
            return $report->setStatus(Status::unknown());
        }

        if ($status["security"] != 0) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }

    public function parse($string)
    {
        $matches = [];
        if (!preg_match(self::REGEXP, $string, $matches)) {
            return null;
        }

        $result["updates"] = $matches[1];
        $result["security"] = $matches[2];
        return $result;
    }
}
