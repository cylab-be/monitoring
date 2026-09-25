<?php

namespace Tests\Unit;

use App\Sensor\Linux\NvidiaSmi;

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
}
