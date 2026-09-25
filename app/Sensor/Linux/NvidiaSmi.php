<?php

namespace App\Sensor\Linux;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

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
        
        return (new Report())
                ->setTitle("Nvidia GPUs")
                ->setHTML(blade(__DIR__ . "/NvidiaSmi.blade.php", ["gpus" => $gpus]))
                ->setStatus(Status::ok());
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
