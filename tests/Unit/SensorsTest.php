<?php

namespace Tests\Unit;

use App\Server;
use App\Sensor\Disks;
use App\Sensor\CPUtemperature;
use App\Sensor\USBtemperature;
use App\Sensor\Ifconfig;
use App\Sensor\Updates;
use App\Sensor\Netstat;

use Tests\TestCase;

/**
 * Description of SensorsTest
 *
 * @author tibo
 */
class SensorsTest extends TestCase
{
    /**
     * @group ifconfig
     * @group sensors
     */
    public function testIfconfig()
    {
        $string = file_get_contents(__DIR__ . "/ifconfig");
        $sensor = new Ifconfig(new Server());
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("enp0s31f6", $interfaces[0]->name);
        $this->assertEquals("10.67.1.32", $interfaces[1]->address);
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
        $sensor = new Ifconfig(new Server());
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("eno1", $interfaces[0]->name);
        $this->assertEquals("172.20.0.8", $interfaces[1]->address);
        $this->assertEquals(185252610, $interfaces[1]->rx);
        $this->assertEquals(266912412, $interfaces[1]->tx);
    }

    /**
     * @group Disks
     */

    public function testDisksSensor()
    {
        $string = file_get_contents(__DIR__ . "/df");
        $sensor = new Disks(new Server());
        $disks = $sensor->parse($string);
        $this->assertEquals(2, count($disks));
        $this->assertEquals("/dev/sda1", $disks[0]->filesystem);
        $this->assertEquals(1128926648, $disks[0]->blocks);
    }

    public function testNetstatListening()
    {
        $string = file_get_contents(__DIR__ . "/netstat-tcp");
        $sensor = new \App\Sensor\ListeningPorts(new Server());
        $ports = $sensor->parse($string);
        $this->assertEquals(16, count($ports));
        $this->assertEquals("31933/cloud-backup-", $ports[4]->process);
        $this->assertEquals(1024, $ports[4]->port);
        $this->assertEquals("127.0.0.1", $ports[4]->bind);
    }

    public function testSsacli()
    {
        $string = file_get_contents(__DIR__ . "/ssacli");
        $sensor = new \App\Sensor\Ssacli(new Server());
        $disks = $sensor->parse($string);
        $this->assertEquals("OK", $disks[0]->status);
    }

    public function testPerccli()
    {
        $string = file_get_contents(__DIR__ . "/perccli");
        $sensor = new \App\Sensor\Perccli(new Server());
        $disks = $sensor->parse($string);
        $this->assertEquals("Onln", $disks[0]->status);
        $this->assertEquals("SSD", $disks[0]->type);
        $this->assertEquals("446.625 GB", $disks[0]->size);
    }

    public function testUpdates()
    {
        $sensor = new Updates(new Server());

        $string1 = "6 packages can be updated.
2 updates are security updates.";
        $status = $sensor->parse($string1);
        $this->assertEquals(2, $status["security"]);

        $string2 = "1 package can be updated.
1 update is a security update.
";
        $status2 = $sensor->parse($string2);
        $this->assertEquals(1, $status2["security"]);
    }
    
    
    /**
     * @group netstat
     */
    public function testNetstat()
    {
        $string = file_get_contents(__DIR__ . "/netstat");
        $server = new Server();
        $netstat = new Netstat($server);
        $this->assertEquals(24004, $netstat->parse($string)->tcp_segments_retransmitted);
    }
    
    
    /**
     * @group CPUtemp
     */
    public function testCPUtemp()
    {
        $string = file_get_contents(__DIR__ . "/sensors");
        $sensor = new CPUtemperature(new Server());
        $CPUTEMPS = $sensor->parse($string);
        $this->assertEquals(4, count($CPUTEMPS));
        $this->assertEquals("Core 3", $CPUTEMPS[3]->name);
        $this->assertEquals("34.0", $CPUTEMPS[3]->value);
    }
    /**
     * @group USBtemp
     */
    public function testTEMPer()
    {
        $string = file_get_contents(__DIR__ . "/TEMPer");
        $TEMPer = new USBtemperature(new Server());
        $USBTemp = $TEMPer->parse($string);
        $this->assertEquals("09", $USBTemp->part1);
        $this->assertEquals("47", $USBTemp->part2);
        $this->assertEquals("23", $USBTemp->temp[1]);
        $this->assertEquals("75", $USBTemp->temp[2]);
    }
    /**
     * @group multicpu
     */
    public function testmultiCPUtemp()
    {
        $string = file_get_contents(__DIR__ . "/sensors");
        $sensor = new CPUtemperature(new Server());
        $CPUTEMPS = $sensor->parseCPUtemperature($string);
        $this->assertEquals(4, count($CPUTEMPS));
        $this->assertEquals("Core 3", $CPUTEMPS[3]->name);
        $this->assertEquals("34.0", $CPUTEMPS[3]->corevalue);
    }
}
