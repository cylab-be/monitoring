<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Illuminate\Database\Eloquent\Collection;

/**
 * Parse netstat
 *
 * @author tibo
 */
class Netstat extends Sensor
{

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "netstat_retransmitted",
            "netstat_statistics",
            "Parse netstat -s to graph ratio of retransmitted TCP segments"
        );
    }

    public function analyze(Record $record): Report
    {
        $records = $record->server->lastRecords("netstat_statistics");
        
        $report = (new Report())->setTitle("Network : Retransmitted TCP segments");
        $report->setHTML(view("agent.netstat", ["dataset" => $this->points($records)]))
                ->setStatus(Status::ok());

        return $report;
    }

    public function points(Collection $records) : Dataset
    {
        $dataset = new Dataset("Retransmitted TCP segments [%]");


        $previous_report = null;
        foreach ($records as $record) {
            $report = NetstatReport::parse($record->data);
            $report->time = $record->time;
            
            // first point in the graph
            if ($previous_report == null) {
                $previous_report = $report;
            }
            
            $sent_segments = $report->tcp_segments_sent - $previous_report->tcp_segments_sent;
            $retransmitted_segments =
                    $report->tcp_segments_retransmitted - $previous_report->tcp_segments_retransmitted;
            $ratio = 0;
            if ($sent_segments != 0) {
                $ratio = (double) $retransmitted_segments / $sent_segments * 100;
            }
            
            // point time is in miliseconds :-(
            $dataset->add(new Point($report->time * 1000, $ratio));
            
            $previous_report = $report;
        }

        return $dataset;
    }
}
