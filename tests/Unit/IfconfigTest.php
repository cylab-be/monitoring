<?php

namespace Tests\Unit;

use App\Sensor\Ifconfig;

use Tests\TestCase;

/**
 * Description of IfconfigTest
 *
 * @author tibo
 */
class IfconfigTest extends TestCase
{
    /**
     * @group ifconfig
     * @group sensors
     */
    public function testIfconfig()
    {
        $string = file_get_contents(__DIR__ . "/ifconfig");
        $sensor = new Ifconfig();
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("enp0s31f6", $interfaces[0]->name);
        $this->assertEquals("10.67.1.32", $interfaces[1]->addresses[0]);
        $this->assertEquals(1074590056, $interfaces[1]->rx);
        $this->assertEquals(2074977132, $interfaces[1]->tx);
    }

    /**
     * Test parsing of ifconfig string from a ubuntu 18.04 server
     *
     * @group ifconfig
     * @group sensors
     */
    public function testIfconfig1804()
    {
        $string = file_get_contents(__DIR__ . "/ifconfig1804");
        $sensor = new Ifconfig();
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(3, count($interfaces));
        $this->assertEquals("eno1", $interfaces[0]->name);
        $this->assertEquals("172.20.0.8", $interfaces[1]->addresses[0]);
        $this->assertEquals(185252610, $interfaces[1]->rx);
        $this->assertEquals(266912412, $interfaces[1]->tx);
    }

    /**
     * @group ifconfig
     * @group sensors
     */
    public function testMultiIP()
    {
        $string = file_get_contents(__DIR__ . "/ifconfig-multi");
        $sensor = new Ifconfig();
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(8, count($interfaces));
        $this->assertEquals("ixl0", $interfaces[0]->name);
        $this->assertEquals(2, count($interfaces[0]->addresses));
        $this->assertEquals("172.20.0.60", $interfaces[0]->addresses[0]);
    }
}
