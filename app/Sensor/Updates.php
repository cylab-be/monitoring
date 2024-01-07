<?php

namespace App\Sensor;

use App\Sensor;
use App\Status;
use App\ServerInfo;
use App\Report;

use Illuminate\Database\Eloquent\Collection;

/**
 * Check if (security) updates are available.
 *
 * @author tibo
 */
class Updates implements Sensor
{

    const REGEXP = "/(\d+)\spackages? can be updated\.\n(\d+)\supdates? (is a|are) security updates?./";
    
    public function analyze(Collection $records, ServerInfo $serverinfo): Report
    {
        $report = new Report("Updates available");
        
        $record = $records->last();
        if (! isset($record->data['updates'])) {
            return $report->setHTML("<p>No data available...</p>");
        }

        $report->setHTML("<p>" . nl2br($record->data["updates"]) . "</p>");
        
        $status = $this->parse($record->data["updates"]);
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
