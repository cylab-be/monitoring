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
        $current_values = $this->extractUtilValuesFrom2Tables($record->data);

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
        /** @var Record $first */
        $values = $this->extractUtilValuesFrom2Tables($first->data);
        $disks = array_keys($values);

        $datasets = [];
        foreach ($disks as $key => $disk) {
            $datasets[$disk] = new Dataset($disk, ColorPalette::pick1Color($key));
        }

        foreach ($records as $record) {
            /** @var Record $record */
            $values = $this->extractUtilValuesFrom2Tables($record->data);
            foreach ($values as $disk => $value) {
                $datasets[$disk]->add(new Point(1000 * $record->time, $value));
            }
        }

        return array_values($datasets);
    }


    public function extractUtilValuesFrom2Tables(string $string) : array
    {
        $table = $this->extract2ndTable($string);
        return $this->extractUtileValuesFromTable($table);
    }
    
    private function extract2ndTable(string $string) : string
    {
        $string = trim($string);
        
        // $string should contain 2 tables
        // each table starts with "Device ..."
        // we must skip the first table and only take into account the second table
        
        // On Linux systems
        $tables = explode("Device            r/s     rkB/s   rrqm/s  %rrqm r_await rareq-sz     w/s     wkB/s   "
                . "wrqm/s  %wrqm w_await wareq-sz     d/s     dkB/s   drqm/s  %drqm d_await dareq-sz     f/s f_await  "
                . "aqu-sz  %util", $string);

        if (count($tables) == 3) {
            return $tables[2];
        }
        
        // On freebsd
        $tables = explode("device       r/s     w/s     kr/s     kw/s  ms/r  ms/w  ms/o  ms/t qlen  %b", $string);
        if (count($tables) == 3) {
            return $tables[2];
        }
        
        throw new \Exception("Could not detect 2 iostat tables");
    }
    
    const SKIP_PREFIXES = ['loop', 'dm-'];
    
    private function extractUtileValuesFromTable(string $table) : array
    {
        $table = str_replace(",", ".", $table);
        $lines = explode("\n", $table);

        $utilValues = [];
        foreach ($lines as $line) {
            $matches = [];
            // Use regex to match the device name and the last number (%util)
            if (preg_match('/^(\S+).*?(\d+(?:\.\d+)?)\s*$/', $line, $matches)) {
                $device = $matches[1];
                if (Str::startsWith($device, self::SKIP_PREFIXES)) {
                    continue;
                }

                $util = (float) $matches[2];
                $utilValues[$device] = $util;
            }
        }

        return $utilValues;
    }
}
