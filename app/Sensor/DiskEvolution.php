<?php



namespace App\Sensor;

class DiskEvolution extends \App\AbstractSensor
{
    const REGEXP = "/\\n([A-z\/0-9:\\-\\.]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/";

    // calculates Deltas for remaining blocks
    
    public function computeEvolution($newAndOld)
    {
                
        foreach ($newAndOld[0] as $key => $partition) {
            $delta = new Delta();
            $delta->filesystem = $partition->filesystem;
            if (($partition->used - $newAndOld[1][$key]->used)== 0) {
                $delta->timeUntillFull = "storage capacity seems to be constant";
            } elseif (($partition->used - $newAndOld[1][$key]->used) < 0) {
                $delta->timeUntillFull = "more storage left then last calculation";
            } else {
                $delta->delta = $partition->used - $newAndOld[1][$key]->used;
                $delta->timeUntillFull = ($partition->blocks - $partition->used)/$delta->delta;
            }
            $deltas[] = $delta;
        }
                return $deltas;
    }
    
    /** gets the records over a certain time
     * then gets the first and the last record
     * and uses parse to make them partition object
     */
     
    public function get2Partitions($timeInterval)
    {
        $newAndOld = [];
        $records = $this->getLastRecords("disks", $timeInterval*12);
        $newPartitions = $this->parse($records[0]->disks);
        $oldPartitions = $this->parse($records[count($records)-1]->disks);
        $newAndOld = [$newPartitions, $oldPartitions];
        return $newAndOld;
    }
    
    // code to print the results
    public function printResults($deltas)
    {
        
        $return = "<table class='table table-sm'>";
        $return .= "<tr><th>name</th><th>time untill full in hours</th></tr>";
    
        foreach ($deltas as $delta) {
                $return .= "<tr><td>". $delta->filesystem . "</td><td>" . $delta->timeUntillFull
                        . "</td></tr>";
        }
        $return .= "</table>";
        return $return;
    }
    
    public function report()
    {
        return $this->printResults($this->computeEvolution($this->get2Partitions(24)));
    }
    public function status()
    {
            $record = $this->getLastRecord("disks");
        if ($record == null) {
            return self::STATUS_UNKNOWN;
        }
                    
        foreach ($this->computeEvolution($this->get2Partitions(24)) as $delta) {
            $status = self::STATUS_OK;
            if ($delta->timeUntillFull == "Not enough messurements") {
                $status = self::STATUS_UNKNOWN;
            } elseif ($delta->timeUntillFull == "more storage left then last calculation") {
                $status = self::STATUS_UNKNOWN;
            } elseif ($delta->timeUntillFull < 48) {
                $status = self::STATUS_WARNING;
            } elseif ($delta->timeUntillFull < 96) {
                $status = self::STATUS_WARNING;
            }
                
            $all_status[] = $status;
        }

        return max($all_status);
    }
        
    
    public static $skip_fs = ["none", "tmpfs", "shm"];

    public function parse($string)
    {
        $values = array();
        preg_match_all(self::REGEXP, $string, $values);
        $partitions = array();
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $fs = $values[1][$i];
            if (in_array($fs, self::$skip_fs)) {
                continue;
            }
            $partition = new Partition();
            $partition->filesystem = $fs;
            $partition->blocks = $values[2][$i];
            $partition->used = $values[3][$i];
            $partition->mounted = $values[6][$i];
            $partitions[] = $partition;
        }
        
        return $partitions;
    }
}
