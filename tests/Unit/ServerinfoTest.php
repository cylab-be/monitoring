<?php

namespace Tests\Unit;

use App\Sensor\MemoryTypes;
use App\ServerInfo;
use App\Sensor\ServerInfoFreeBSDCPU;

use Tests\TestCase;

/**
 * Description of MemoryTest
 *
 * @group memory
 * @author tibo
 */
class ServerinfoTest extends TestCase
{
    /**
     * @group sensors
     */
    public function testMemoryTypes()
    {
        $string = file_get_contents(__DIR__ . "/memory-dmi");
        $sensor = new MemoryTypes();

        $dims = $sensor->parse($string);
        $this->assertEquals(4, count($dims));
        $this->assertEquals(32, $dims[0]->size);
    }

    /**
     * @group freebsd
     * @group cpu
     * @group freebsd-cpu
     */
    public function testThreadsFreebsd()
    {
        $string = file_get_contents(__DIR__ . "/freebsd-dmi-cpu");
        $sensor = new ServerInfoFreeBSDCPU();
        $info = new ServerInfo();
        $sensor->analyzeString($string, $info);
        $this->assertEquals(4, $info->vCores());
    }
    
    /**
     * Test CPU cores extraction for a system with multiple processors and many threads
     * 
     * @group cpu
     */
    public function testThreadsMulti()
    {
        $string = file_get_contents(__DIR__ . "/dmi-cpu");
        $sensor = new ServerInfoFreeBSDCPU();
        $info = new ServerInfo();
        $sensor->analyzeString($string, $info);
        $this->assertEquals(64, $info->vCores());
    }
}
