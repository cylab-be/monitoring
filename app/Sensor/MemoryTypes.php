<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
 * Determine the type and number of memory slots and dims.
 *
 * @author tibo
 */
class MemoryTypes implements Sensor
{

    const REGEXES = [
        "size"              => '/^\tSize: (\d+) GB/m',
        "locator"           => '/^\tLocator: (.*)/m',
        "type"              => '/^\tType: (.*)/m',
        "speed"             => '/^\tSpeed: (.*)/m',
        "manufacturer"      => '/^\tManufacturer: (.*)/m',
        "part_number"       => '/^\tPart Number: (.*)/m',
        "configured_speed"  => '/^\tConfigured Memory Speed: (.*)/m',

    ];


    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Memory : Type");
        $report->setStatus(Status::ok());

        $dims = $this->parse($record->data);
        $report->setHTML(view("sensor.memorytypes", ["dims" => $dims]));
        return $report;
    }

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "memory_types",
            "memory_dmi",
            "Parse DMI memory info to list installed memory modules and available slots"
        );
    }

    public function parse(string $string)
    {
        $dims = [];

        $lines = explode("\n", $string);
        $dim = null;
        foreach ($lines as $line) {
            if ($line == "Memory Device") {
                $dim = new MemoryDevice();
                $dims[] = $dim;
                continue;
            }

            $match = [];

            foreach (self::REGEXES as $field => $regex) {
                if (preg_match($regex, $line, $match) === 1) {
                    $dim->$field = $match[1];
                    continue 2;
                }
            }
        }

        return $dims;
    }
}
