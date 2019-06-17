<?php

namespace Tests\Unit;

use App\User;
use App\Organization;
use App\Sensor\Disks;
use App\Sensor\Ifconfig;

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

    public function testRelations()
    {
        $user = new User();
        $user->name = "test";
        $user->email = "test@example.com";
        $user->password = "abc123";
        $user->save();

        $organization = new Organization();
        $organization->name = "Org";
        $organization->save();

        $organization->users()->save($user);

        $this->assertEquals(
            "Org",
            $user->organizations()->first()->name
        );
    }

    /**
     * @group ifconfig
     * @group sensors
     */
    public function testIfconfig()
    {
        $string = file_get_contents(__DIR__ . "/ifconfig");
        $sensor = new Ifconfig(new \App\Server());
        $interfaces = $sensor->parseIfconfig($string);
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("enp0s31f6", $interfaces[0]->name);
        $this->assertEquals("10.67.1.32", $interfaces[1]->address);
        $this->assertEquals(1074590056, $interfaces[1]->rx);
        $this->assertEquals(2074977132, $interfaces[1]->tx);
    }

    public function testDisksSensor()
    {
        $string = file_get_contents(__DIR__ . "/df");
        $sensor = new Disks(new \App\Server());
        $disks = $sensor->parse($string);
        $this->assertEquals(3, count($disks));
        $this->assertEquals("/dev/sda1", $disks[1]->filesystem);
        $this->assertEquals(1128926648, $disks[1]->blocks);
    }

    public function testSsacli()
    {
        $string = file_get_contents(__DIR__ . "/ssacli");
        $sensor = new \App\Sensor\Ssacli(new \App\Server());
        $disks = $sensor->parse($string);
        $this->assertEquals("OK", $disks[0]->status);
    }

    public function testUpdates()
    {
        $sensor = new \App\Sensor\Updates(new \App\Server());

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

    public function testMeminfo()
    {
        $string = file_get_contents(__DIR__ . "/meminfo");
        $server = new \App\Server();
        $mem_total = $server->parseMeminfo($string);
        $this->assertEquals("15954328", $mem_total);
    }

    /**
     * @group cpuinfo
     */
    public function testCpuinfo()
    {
        $string = file_get_contents(__DIR__ . "/cpuinfo");
        $server = new \App\Server();
        $cpuinfo = $server->parseCpuinfo($string);
        $this->assertEquals(8, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);
    }

    /**
     * @group cpuinfo
     */
    public function testCpuinfoSingleCPU()
    {
        $string = file_get_contents(__DIR__ . "/cpuinfo_1cpu");
        $server = new \App\Server();
        $cpuinfo = $server->parseCpuinfo($string);
        $this->assertEquals(1, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);
    }

    public function testManufacturer()
    {
        $string = file_get_contents(__DIR__ . "/system");
        $server = new \App\Server();
        $manufacturer = $server->parseManufacturer($string);
        $this->assertEquals("LENOVO", $manufacturer);
    }

    public function testProductName()
    {
        $string = file_get_contents(__DIR__ . "/system");
        $server = new \App\Server();
        $manufacturer = $server->parseProductName($string);
        $this->assertEquals("20J60018MB", $manufacturer);
    }

    public function testClientVersion()
    {
        $server = new \App\Server();
        $client_version = new \App\Sensor\ClientVersion($server);
        $this->assertStringMatchesFormat('%d.%d.%d', $client_version->latestVersion());
    }

    /**
     * @group status-change
     */
    public function testStatusChangeDetection()
    {
        $organization = new Organization();
        $organization->name = "ACME";
        $organization->save();

        $server = new \App\Server();
        $server->name = "My test server";
        $server->organization()->associate($organization);
        $server->save();

        $server_id = $server->id;

        $user = new User();
        $user->name = "Test";
        $user->email = "thibault.debatty@gmail.com";
        $user->password = "qmlskdj";
        $user->save();
        $organization->users()->attach($user->id);

        $this->assertEquals($server_id, \App\StatusChange::getLastChangeForServer(1)->server_id);

        // Insert a fake status change
        $change = new \App\StatusChange();
        $change->status = 155;
        $change->server_id = $server_id;
        \App\StatusChange::save($change);

        // Run change detection
        $change_detection_job = new \App\Jobs\StatusChangeDetection();
        $change_detection_job->detectChangeForServer($server);

        // Check if a new StatusChange was inserted in Mongo
        $last_change = \App\StatusChange::getLastChangeForServer($server_id);
        $this->assertEquals(
            $server->status(),
            $last_change->status
        );
    }
}
