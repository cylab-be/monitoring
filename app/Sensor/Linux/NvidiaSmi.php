<?php

namespace App\Sensor\Linux;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

use App\Sensor\Dataset;
use App\Sensor\Point;

use Illuminate\Database\Eloquent\Collection;

/**
 * Description of NvidiaSmi
 *
 * @author tibo
 */
class NvidiaSmi extends Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "nvidia-smi",
            "nvidia-smi",
            "Parse nvidia-smi data to get metrics on Nvidia gpus.",
            ["nvidia-smi" => "command -v nvidia-smi >/dev/null 2>&1 && "
            . "nvidia-smi --query-gpu=index,name,utilization.gpu,utilization.memory,memory.used,memory.total,"
            . "temperature.gpu --format=csv",]
        );
    }
    
    public function analyze(Record $record): ?Report
    {
        $gpus = $this->parse($record->data);
        
        // analyze data for last 24h
        $records24h = $record->server->lastRecords("nvidia-smi");
        $datasets = $this->buildDatasets($records24h);
        
        return (new Report())
                ->setTitle("Nvidia GPUs")
                ->setHTML(blade(__DIR__ . "/NvidiaSmi.blade.php", [
                    "gpus" => $gpus,
                    "datasets" => $datasets]))
                ->setStatus(Status::ok());
    }
    
    /**
     * Return one dataset for each GPU.
     *
     * @param Collection $records
     * @return array<Dataset>
     */
    public function buildDatasets(Collection $records) : array
    {
        $datasets = [];
        
        /** @var Record $first_record */
        $first_record = $records->first();
        $gpus = $this->parse($first_record->data);
        foreach ($gpus as $gpu) {
            $datasets[$gpu["index"]] = new Dataset($gpu["index"]);
        }
        
        foreach ($records as $record) {
            /** @var Record $record */
            $gpus = $this->parse($record->data);
            
            foreach ($gpus as $gpu) {
                $datasets[$gpu["index"]]->add(new Point(
                    $record->time * 1000,
                    $gpu["utilization_gpu_pct"]
                ));
            }
        }
        return $datasets;
    }
    
    /**
     *
     * Parse lines like
     * index, name, utilization.gpu [%], utilization.memory [%], memory.used [MiB], memory.total [MiB], temperature.gpu
     * 0, NVIDIA GeForce RTX 5090, 0 %, 0 %, 19558 MiB, 32607 MiB, 45
     *
     * @param string $data
     * @return array
     */
    public function parse(string $data) : array
    {
        $pattern = '/^(\d+),\s*(.+?),\s*(\d+)\s*%,\s*(\d+)\s*%,\s*(\d+)\s*MiB,\s*(\d+)\s*MiB,\s*(\d+)$/m';

        $matches = [];
        // PREG_SET_ORDER makes $matches an array of matches, where each match is its own array
        if (! preg_match_all($pattern, $data, $matches, PREG_SET_ORDER)) {
            return [];
        }
        
        
        $gpus = [];
        foreach ($matches as $match) {
            $gpus[] = [
                'index' => (int) $match[1],
                'name' => trim($match[2]),
                'utilization_gpu_pct' => (int) $match[3],
                'utilization_mem_pct' => (int) $match[4],
                'memory_used_mib' => (int) $match[5],
                'memory_total_mib' => (int) $match[6],
                'temperature_gpu' => (int) $match[7],
            ];
        }
        
        return $gpus;
    }
}
