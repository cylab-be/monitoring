<?php

namespace App\Sensor\Linux;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use App\Sensor\Dataset;
use App\Sensor\Point;
use App\Sensor\ColorPalette;

use Illuminate\Database\Eloquent\Collection;

/**
     * Description of Update
 *
 * @author helha
 */
class CPUtemperature extends Sensor
{
    // Match a CPU line like
    // Package id 0:  +39.0°C  (high = +84.0°C, crit = +100.0°C)
    const REGEXPCPU = "/^(Package id)+\s+(\d):\s+\+(\d+\.\d+)°C\s+\(high\s=\s\+\d+\.\d°C,\scrit\s=\s\+(\d+\.\d+)°C\)/m";

    // Mach a core line
    // Core 0:        +38.0°C  (high = +84.0°C, crit = +100.0°C)
    const REGEXPCORE = "/^(Core \d+):\s+\+(\d+\.\d+)°C\s+\(high\s=\s\+\d+\.\d°C,\scrit\s=\s\+(\d+\.\d+)°C\)/m";

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "cpu_temperature",
            "cpu_temperature",
            "Uses lm-sensors package to extract CPU temperature"
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("CPU : Temperature");

        $cpus = $this->parse($record->data);

        // analyze data for last 24h
        $records24h = $record->server->lastRecords("cpu_temperature");
        $datasets = $this->buildDatasets($records24h);

        $report->setHTML(blade(__DIR__ . "/CPUtemperature.blade.php", [
            "cpus" => $cpus,
            "datasets" => $datasets
        ]));
        $report->setStatus(Status::max($cpus));

        return $report;
    }

    /**
     * Return one dataset per CPU package.
     *
     * @param Collection $records
     * @return array<Dataset>
     */
    public function buildDatasets(Collection $records) : array
    {
        $datasets = [];
        $palette = new ColorPalette();

        /** @var Record $first_record */
        $first_record = $records->first();
        $cpus = $this->parse($first_record->data);
        foreach ($cpus as $cpu) {
            $datasets[$cpu->name] = new Dataset("CPU " . $cpu->name, $palette->next());
        }

        foreach ($records as $record) {
            /** @var Record $record */
            $cpus = $this->parse($record->data);

            foreach ($cpus as $cpu) {
                $datasets[$cpu->name]->add(new Point(
                    $record->time * 1000,
                    $cpu->value
                ));
            }
        }
        return $datasets;
    }

    public function parse(string $string)
    {
        $cpus = [];
        $cpu = null;

        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $match = [];

            // this line corresponds to a CPU definition
            if (preg_match(self::REGEXPCPU, $line, $match) === 1) {
                $cpu = new Cpu($match[2], (float) $match[3], (float) $match[4]);
                $cpus[] = $cpu;
                continue;
            }

            // line correponds to a core definition
            if (preg_match(self::REGEXPCORE, $line, $match) === 1) {
                $core = new Core($match[1], (float) $match[2], (float) $match[3]);
                // append to current CPU
                $cpu->cores[] = $core;
                continue;
            }
        }
        return $cpus;
    }
}
