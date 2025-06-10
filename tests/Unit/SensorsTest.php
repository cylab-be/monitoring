<?php

namespace Tests\Unit;

use App\Status;

use App\Sensor\Disks;
use App\Sensor\CPUtemperature;
use App\Sensor\Updates;
use App\Sensor\NetstatReport;
use App\Sensor\Temper;

use Tests\TestCase;

/**
 * Description of SensorsTest
 *
 * @author tibo
 */
class SensorsTest extends TestCase
{
    /**
     * @group disks
     */
    public function testDiskActivity()
    {
        $string = file_get_contents(__DIR__ . "/iostat");
        $sensor = new \App\Sensor\DiskActivity();
        $values = $sensor->extractUtilValues($string);
        $this->assertEquals(5.65, $values["sdb"]);
    }


    /**
     * @group Disks
     */
    public function testDisksSensor()
    {
        $string = file_get_contents(__DIR__ . "/df");
        $sensor = new Disks();
        $disks = $sensor->parse($string);
        $this->assertEquals(2, count($disks));
        $this->assertEquals("/dev/sda1", $disks[0]->filesystem);
        $this->assertEquals(1128926648, $disks[0]->blocks);
    }

    public function testNetstatListening()
    {
        $string = file_get_contents(__DIR__ . "/netstat-tcp");
        $sensor = new \App\Sensor\ListeningPorts();
        $ports = $sensor->parse($string);
        $this->assertEquals(4, count($ports));
        $this->assertEquals(111, $ports[0]->port);
        $this->assertEquals("0.0.0.0", $ports[0]->bind);
    }

    public function testSsacli()
    {
        $string = file_get_contents(__DIR__ . "/ssacli");
        $sensor = new \App\Sensor\Ssacli();
        $disks = $sensor->parse($string);
        $this->assertEquals("OK", $disks[0]->status);
    }

    public function testPerccli()
    {
        $string = file_get_contents(__DIR__ . "/perccli");
        $sensor = new \App\Sensor\Perccli();
        $disks = $sensor->parse($string);

        $this->assertEquals(Status::ok(), $disks[0]->status);
        $this->assertEquals("SSD", $disks[0]->type);
        $this->assertEquals("446.625 GB", $disks[0]->size);
    }

    public function testUpdates()
    {
        $sensor = new Updates();

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
        $this->assertEquals(24004, NetstatReport::parse($string)->tcp_segments_retransmitted);
    }

    /**
     * @group netstat
     */
    public function testNetstatMint()
    {
        $string = file_get_contents(__DIR__ . "/netstat-mint");
        $report = NetstatReport::parse($string);
        $this->assertEquals(50555, $report->tcp_segments_retransmitted);
        $this->assertEquals(6161477, $report->tcp_segments_sent);
    }


    /**
     * @group CPUtemp
     */
    public function testCPUtemp()
    {
        $string = file_get_contents(__DIR__ . "/sensors");
        $sensor = new CPUtemperature();

        $cpus = $sensor->parse($string);
        $this->assertEquals(4, count($cpus[0]->cores));
        $this->assertEquals("Core 3", $cpus[0]->cores[3]->name);
        $this->assertEquals("34.0", $cpus[0]->cores[3]->value);
    }
    /**
     * @group USBtemp
     */
    public function testTEMPer()
    {
        $string = file_get_contents(__DIR__ . "/TEMPer");

        $temper = new Temper();
        $t = $temper->convert($string);
        $this->assertEquals(23.75, $t);
    }
}
