<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "loadavg",
            "loadavg",
            "Parse /proc/loadavg to check CPU load"
        );
    }


    public function analyze(Record $record): Report
    {
        $server = $record->server;

        $warning_threshold = $server->info->vCores();
        $error_threshold = 2 * $warning_threshold;
        $current_load = $this->parse($record->data);

        $records = $server->lastRecords($record->label);
        $max_load = $records
                ->map(function (Record $record) {
                    return $this->parse($record->data);
                })
                ->max();

        $report = (new Report())->setTitle("CPU : Load");
        $report->setHTML(view("agent.loadavg", [
            "current_load" => $current_load,
            "warning_threshold" => $warning_threshold,
            "error_threshold" => $error_threshold,
            "max_load" => $max_load,
            "dataset" => $this->loadPoints($server->lastRecords("loadavg"))]));

        if ($max_load > $error_threshold) {
            return $report->setStatus(Status::error());
        }

        if ($max_load > $warning_threshold) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }

    public function loadPoints(Collection $records) : Dataset
    {
        $dataset = new Dataset("Load");
        foreach ($records as $record) {
            $dataset->add(new Point(
                $record->time * 1000,
                $this->parse($record->data)
            ));
        }
        return $dataset;
    }

    public function parse(string $string) : float
    {
        return floatval(current(explode(" ", $string)));
    }
}
