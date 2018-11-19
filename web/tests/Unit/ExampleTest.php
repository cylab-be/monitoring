<?php

namespace Tests\Unit;

use App\User;
use App\Organization;
use App\Sensor\Disks;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    public function testRelations() {
        $user = new User();
        $user->name = "test";
        $user->email = "test@example.com";
        $user->password = "abc123";
        $user->save();

        $organization = new Organization();
        $organization->name = "Org";
        $organization->save();

        $organization->users()->save($user);

        $this->assertEquals("Org", $user->organizations()->first()->name);
    }

    public function testDisksSensor() {
        $string = file_get_contents(__DIR__ . "/df");
        $sensor = new Disks(new \App\Server());
        $disks = $sensor->parse($string);
        $this->assertEquals(3, count($disks));
        $this->assertEquals("/dev/sda1", $disks[1]->filesystem);
        $this->assertEquals(1128926648, $disks[1]->blocks);
    }

    public function testSsacli() {
        $string = file_get_contents(__DIR__ . "/ssacli");
        $sensor = new \App\Sensor\Ssacli(new \App\Server());
        $disks = $sensor->parse($string);
        $this->assertEquals("OK", $disks[0]->status);
    }

    public function testUpdates() {
        $string = "6 packages can be updated.
2 updates are security updates.";

        $sensor = new \App\Sensor\Updates(new \App\Server());
        $status = $sensor->parse($string);
        $this->assertEquals(2, $status["security"]);
    }

    public function testMeminfo() {
        $string = file_get_contents(__DIR__ . "/meminfo");
        $server = new \App\Server();
        $mem_total = $server->parseMeminfo($string);
        $this->assertEquals("15954328", $mem_total);
    }

    public function testCpuinfo() {
        $string = file_get_contents(__DIR__ . "/cpuinfo");
        $server = new \App\Server();
        $cpuinfo = $server->parseCpuinfo($string);
        $this->assertEquals(8, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);
    }

    public function testManufacturer() {
        $string = file_get_contents(__DIR__ . "/system");
        $server = new \App\Server();
        $manufacturer = $server->parseManufacturer($string);
        $this->assertEquals("LENOVO", $manufacturer);
    }

    public function testVersion() {
        $string = file_get_contents(__DIR__ . "/system");
        $server = new \App\Server();
        $manufacturer = $server->parseVersion($string);
        $this->assertEquals("ThinkPad T470p", $manufacturer);
    }

    public function testClientVersion() {
        $server = new \App\Server();
        $client_version = new \App\Sensor\ClientVersion($server);
        $this->assertStringMatchesFormat('%f', $client_version->latestVersion());
    }


}
