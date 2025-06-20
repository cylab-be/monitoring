<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Record;
use App\Report;
use App\Status;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Extract disk activity (in %) from iostat data.
 *
 * @author tibo
 */
class DiskActivity extends Sensor
{

    public function config(): SensorConfig
    {
        return new SensorConfig(
            "disk_activity",
            "iostat",
            "Extract disk activity (%util) from iostat output"
        );
    }

    public function analyze(Record $record): ?Report
    {
        $records = $record->server->lastRecords("iostat");
        $current_values = $this->extractUtilValues($record->data);

        $report = new Report();
        $report->setTitle("Storage : disk activity")
                ->setStatus(Status::ok())
                ->setHTML(view("sensor.diskactivity", [
                    "values" => $current_values,
                    "datasets" => $this->extractDatasets($records)]));
        return $report;
    }

    /**
     *
     * @param Collection<Record> $records
     * @return Array<Dataset>
     */
    public function extractDatasets(Collection $records) : array
    {
        $first = $records->first();
        $values = $this->extractUtilValues($first->data);
        $disks = array_keys($values);

        $datasets = [];
        foreach ($disks as $key => $disk) {
            $datasets[$disk] = new Dataset($disk, ColorPalette::pick1Color($key));
        }

        foreach ($records as $record) {
            /** @var Record $record */
            $values = $this->extractUtilValues($record->data);
            foreach ($values as $disk => $value) {
                $datasets[$disk]->add(new Point(1000 * $record->time, $value));
            }
        }

        return array_values($datasets);
    }


    public function extractUtilValues(string $string) : array
    {
        // $string should contain 2 tables
        // each table starts with "Device ..."
        // we must skip the first table and only take into account the second table
        $tables = explode("Device            r/s     rkB/s   rrqm/s  %rrqm r_await rareq-sz     w/s     wkB/s   "
                . "wrqm/s  %wrqm w_await wareq-sz     d/s     dkB/s   drqm/s  %drqm d_await dareq-sz     f/s f_await  "
                . "aqu-sz  %util", trim($string));

        if (count($tables) != 3) {
            throw new \Exception("Could not detect 2 iostat tables");
        }

        $table = $tables[2];
        $table = str_replace(",", ".", $table);
        $lines = explode("\n", $table);

        $utilValues = [];
        foreach ($lines as $line) {
            $matches = [];
            // Use regex to match the device name and the last number (%util)
            if (preg_match('/^(\S+).*?(\d+\.\d+)\s*$/', $line, $matches)) {
                $device = $matches[1];
                if (Str::startsWith($device, "loop")) {
                    continue;
                }

                $util = (float) $matches[2];
                $utilValues[$device] = $util;
            }
        }

        return $utilValues;
    }
}
