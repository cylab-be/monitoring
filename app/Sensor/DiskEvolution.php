<?php

namespace App\Sensor;

class DiskEvolution extends \App\AbstractSensor
{


    /**
     * Compute deltas between two arrays of partitions
     * @param Partition[] $newAndOld
     * @return \App\Sensor\Delta[]
     */
    public function computeEvolution(array $newAndOld, int $timeDifference) : array
    {

        foreach ($newAndOld[0] as $key => $partition) {
            $delta = new Delta();
            $delta->filesystem = $partition->filesystem;
            $delta->delta = $partition->used - $newAndOld[1][$key]->used;
            if ($delta->delta == 0) {
                $delta->timeUntillFull = PHP_INT_MAX;
            } else {
                $delta->timeUntillFull = ($partition->blocks - $partition->used) / $delta->delta * $timeDifference;
            }
            $deltas[] = $delta;
        }
        return $deltas;
    }

    /**
     * Gets the records over a certain time
     * then gets the first and the last record
     * and uses parse to make them partition object
     *
     * @param int $timeInterval in hours
     */
    public function get2Partitions(int $timeInterval)
    {
        $records = $this->getLastRecords("disks", $timeInterval * 12);
        $newPartitions = Disks::parse($records[0]->disks);
        $oldPartitions = Disks::parse($records[count($records) - 1]->disks);
        $newAndOld = [$newPartitions, $oldPartitions];
        return $newAndOld;
    }

    // code to print the results
    public function printResults($deltas)
    {

        $return = "<table class='table table-sm'>";
        $return .= "<tr><th>name</th><th>time untill full (h)</th></tr>";

        foreach ($deltas as $delta) {
            $return .= "<tr>"
                    . "<td>" . $delta->filesystem . "</td>"
                    . "<td>" . $delta->timeUntillFull . "</td>"
                    . "</tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function report()
    {
        return $this->printResults(
            $this->computeEvolution($this->get2Partitions(24), 24)
        );
    }

    public function status()
    {

        $deltas = $this->computeEvolution($this->get2Partitions(24), 24);
        return $this->computeStatusFromDeltas($deltas);
    }

    public function computeStatusFromDeltas(array $deltas)
    {
        foreach ($deltas as $delta) {
            $status = self::STATUS_OK;

            if ($delta->timeUntillFull > 0
                    && $delta->timeUntillFull < 96) {
                $status = self::STATUS_WARNING;
            }

            $all_status[] = $status;
        }

        return max($all_status);
    }
}
