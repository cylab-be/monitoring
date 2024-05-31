<?php

namespace Tests\Unit;

use App\ServerInfoParser;

/**
 * Description of ServerInfoParserTest
 *
 * @author tibo
 */
class ServerInfoParserTest
{
    public function testMeminfo()
    {
        $string = file_get_contents(__DIR__ . "/meminfo");
        $mem_total = (new ServerInfoParser())->parseMeminfo($string);
        $this->assertEquals("15954328", $mem_total);
    }

    /**
     * @group cpuinfo
     */
    public function testCpuinfo()
    {
        $string = file_get_contents(__DIR__ . "/cpuinfo");
        $cpuinfo = (new ServerInfoParser())->parseCpuinfo($string);
        $this->assertEquals(8, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);
    }
    
    /**
     * @group cpuinfo
     */
    public function testCpuinfoRaspberry()
    {
        $string = file_get_contents(__DIR__ . "/cpuinfo-raspberry");
        $cpuinfo = (new ServerInfoParser())->parseCpuinfo($string);
        $this->assertEquals(4, $cpuinfo["threads"]);
        $this->assertEquals("Raspberry Pi 4 Model B Rev 1.1", $cpuinfo["cpu"]);
    }
    
    /**
     * @group cpuinfo
     */
    public function testCpuinfoSingleCPU()
    {
        $string = file_get_contents(__DIR__ . "/cpuinfo_1cpu");
        $cpuinfo = (new ServerInfoParser())->parseCpuinfo($string);
        $this->assertEquals(1, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);
    }

    /**
     * @group uptime
     */
    public function testUptime()
    {
        $string = "24439.45 190434.65";
        $uptime = (new ServerInfoParser())->parseUptime($string);
        $this->assertEquals("6 hours", $uptime);
    }

    public function testUUID()
    {
        $uuid = (new ServerInfoParser())->parseUUID(file_get_contents(__DIR__ . "/system"));
        $this->assertEquals("74F7C34C-2924-11B2-A85C-DC427DCA7109", $uuid);
    }

    public function testManufacturer()
    {
        $string = file_get_contents(__DIR__ . "/system");
        $manufacturer = (new ServerInfoParser())->parseManufacturer($string);
        $this->assertEquals("LENOVO", $manufacturer);
    }

    public function testProductName()
    {
        $string = file_get_contents(__DIR__ . "/system");
        $manufacturer = (new ServerInfoParser())->parseProductName($string);
        $this->assertEquals("20J60018MB", $manufacturer);
    }
}
