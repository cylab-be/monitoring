<?php

namespace App\Sensor\Linux;

use App\Sensor;
use App\Sensor\Dataset;
use App\Sensor\Point;

use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of CPUPressure
 *
 * Parses /proc/pressure/cpu (PSI - Pressure Stall Information) to measure
 * how often tasks were stalled waiting for CPU.
 *
 * @author tibo
 */
class CPUPressure extends Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "cpu_pressure",
            "cpu_pressure",
            "Parse /proc/pressure/cpu to check CPU pressure (PSI)",
            ["cpu_pressure" => "cat /proc/pressure/cpu"]
        );
    }

    // Pressure is a percentage (0-100), so no core-count scaling is needed.
    const WARNING_THRESHOLD = 10;
    const ERROR_THRESHOLD = 30;


    public function analyze(Record $record): Report
    {
        $server = $record->server;

        $warning_threshold = self::WARNING_THRESHOLD;
        $error_threshold = self::ERROR_THRESHOLD;
        $current_pressure = $this->parse($record->data);

        $records = $server->lastRecords($record->label);
        $max_pressure = $records
                ->map(function ($record) {
                    /** @var Record $record */
                    return $this->parse($record->data);
                })
                ->max();

        $report = (new Report())->setTitle("CPU : Pressure (PSI)");
        $report->setHTML(blade(__DIR__ . '/CPUPressure.blade.php', [
            "current_pressure" => $current_pressure,
            "warning_threshold" => $warning_threshold,
            "error_threshold" => $error_threshold,
            "max_pressure" => $max_pressure,
            "dataset" => $this->pressurePoints($server->lastRecords("cpu_pressure"))]));

        if ($max_pressure > $error_threshold) {
            return $report->setStatus(Status::error());
        }

        if ($max_pressure > $warning_threshold) {
            return $report->setStatus(Status::warning());
        }

        return $report->setStatus(Status::ok());
    }

    /**
     *
     * @param Collection<Record> $records
     * @return Dataset
     */
    public function pressurePoints(Collection $records) : Dataset
    {
        $dataset = new Dataset("CPU pressure");
        foreach ($records as $record) {
            $dataset->add(new Point(
                $record->time * 1000,
                $this->parse($record->data)
            ));
        }
        return $dataset;
    }

    /**
     * Extract the "some" avg300 (5 minutes average) from /proc/pressure/cpu:
     *
     *     some avg10=0.00 avg60=0.00 avg300=0.00 total=2307807811
     *     full avg10=0.00 avg60=0.00 avg300=0.00 total=0
     *
     * @return float percentage (0-100)
     */
    public function parse(string $string) : float
    {
        if (preg_match('/^some.*avg300=([0-9.]+)/m', $string, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }
}
