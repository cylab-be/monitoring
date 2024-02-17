<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\ServerInfo;
use App\Report;
use App\Status;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Collection;
/**
 * Description of Update
 *
 * @author tibo
 */
class Inodes implements Sensor
{
    public function config(): SensorConfig 
    {
        return new SensorConfig("inodes", "inodes");
    }

    const REGEXP = "/\\n([A-z\/0-9:\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";
    
    public function analyze(DatabaseCollection $records, ServerInfo $serverinfo): Report
    {
        $report = (new Report())->setTitle("Inodes");
        
        $record = $records->last();
        $disks = $this->parse($record->data);
        $report->setHTML(view("sensor.inodes", ["disks" => $disks]));
        
        return $report->setStatus(Status::max($disks));
    }

    public function parse(string $string) : Collection
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        
        $disks = new Collection();
        $count = count($values[1]);
        
        $disks_sensor = new Disks();
        
        for ($i = 0; $i < $count; $i++) {
            $fs = $values[1][$i];
            
            if ($disks_sensor->shouldSkip($fs)) {
                continue;
            }

            $disk = new InodesDisk();
            $disk->filesystem = $values[1][$i];
            $disk->inodes = $values[2][$i];
            $disk->used = $values[3][$i];
            $disk->mounted = $values[6][$i];
            $disks->push($disk);
        }
        return $disks;
    }
}
