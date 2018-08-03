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
        $string = "Filesystem      1K-blocks    Used  Available Use% Mounted on
udev             12238236       0   12238236   0% /dev
tmpfs             2451716  264052    2187664  11% /run
/dev/sda1      1128926648 6545484 1065011924   1% /
tmpfs            12258572       4   12258568   1% /dev/shm
tmpfs                5120       0       5120   0% /run/lock
tmpfs            12258572       0   12258572   0% /sys/fs/cgroup
tmpfs             2451716       0    2451716   0% /run/user/1000";

        $sensor = new Disks(new \App\Server());
        $disks = $sensor->parse($string);
        $this->assertEquals(7, count($disks));
        $this->assertEquals("/dev/sda1", $disks[2]->filesystem);
        $this->assertEquals(1128926648, $disks[2]->blocks);
    }

    public function testUpdates() {
        $string = "6 packages can be updated.
2 updates are security updates.";

        $sensor = new \App\Sensor\Updates(new \App\Server());
        $status = $sensor->parse($string);
        $this->assertEquals(2, $status["security"]);
    }

    public function testCpuinfo() {
        $string = file_get_contents(__DIR__ . "/cpuinfo");

        $server = new \App\Server();
        $cpuinfo = $server->parseCpuinfo($string);
        $this->assertEquals(8, $cpuinfo["threads"]);
        $this->assertEquals("Intel(R) Core(TM) i7-7700HQ CPU @ 2.80GHz", $cpuinfo["cpu"]);

    }


}
