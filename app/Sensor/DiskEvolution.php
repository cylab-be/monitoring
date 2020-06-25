<?php

namespace App\Sensor;

class DiskEvolution extends \App\AbstractSensor
{


    /**
     * Compute deltas between two arrays of partitions
     * @param Partition[] $newAndOld
     * @return \App\Sensor\Delta[]
     */
    public function computeEvolution(?array $newAndOld, int $timeDifference) : array
    {
        if ($newAndOld == null) {
            // can happen if we have no records for this server
            return [];
        }

        $deltas = [];
        foreach ($newAndOld[0] as $key => $partition) {
            if (!isset($newAndOld[1][$key])) {
                continue;
            }

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
     *
     * @param int $timeInterval
     * @return array|null
     */
    public function get2Partitions(array $records) : ?array
    {
        if (count($records) < 2) {
            return null;
        }

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

    public function report(array $records) : string
    {
        return $this->printResults(
            $this->computeEvolution($this->get2Partitions($records), 24)
        );
    }

    public function status(array $records) : int
    {

        $deltas = $this->computeEvolution($this->get2Partitions($records), 24);
        return $this->computeStatusFromDeltas($deltas);
    }

    public function computeStatusFromDeltas(array $deltas) : int
    {
        if (count($deltas) == 0) {
            return \App\Status::UNKNOWN;
        }

        $all_status = [];
        foreach ($deltas as $delta) {
            $status = \App\Status::OK;

            if ($delta->timeUntillFull > 0
                    && $delta->timeUntillFull < 96) {
                $status = \App\Status::WARNING;
            }

            $all_status[] = $status;
        }
        return max($all_status);
    }
}
