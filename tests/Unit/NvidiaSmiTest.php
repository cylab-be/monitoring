<?php

namespace Tests\Unit;

use App\Sensor\Linux\NvidiaSmi;
use App\Record;
use Illuminate\Database\Eloquent\Collection;

use Tests\TestCase;

class NvidiaSmiTest extends TestCase
{
    public function testParse()
    {
        $data = file_get_contents(__DIR__ . "/NvidiaSmi");
        $sensor = new NvidiaSmi;
        $gpus = $sensor->parse($data);
        
        $this->assertEquals("NVIDIA GeForce RTX 5090", $gpus[0]["name"]);
    }

    public function testBuildDatasets()
    {
        $data = file_get_contents(__DIR__ . "/NvidiaSmi");
        $sensor = new NvidiaSmi;

        $record = new Record;
        $record->time = 1700000000;
        $record->data = $data;

        $records = new Collection([$record]);

        $datasets = $sensor->buildDatasets($records, "utilization_gpu_pct");
        $this->assertEquals(12, $datasets[0]->data[0]->y);

        $memory = $sensor->buildDatasets($records, "memory_used_mib");
        $this->assertEquals(19332, $memory[0]->data[0]->y);
    }
}
