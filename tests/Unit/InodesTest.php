<?php

namespace Tests\Unit;

use App\Status;
use App\Sensor\Inodes;
use App\Sensor\InodesDisk;

use Tests\TestCase;

/**
 * Description of InodesTest
 *
 * @author tibo
 */
class InodesTest extends TestCase
{
    public function testParseDfOutput()
    {
        $string = file_get_contents(__DIR__ . "/df");
        $disks = (new Inodes())->parse($string);

        // udev / tmpfs lines must be skipped
        $this->assertCount(2, $disks);

        $disk = $disks->first();
        $this->assertInstanceOf(InodesDisk::class, $disk);
        $this->assertEquals("/dev/sda1", $disk->filesystem);
        $this->assertEquals(1128926648, $disk->inodes);
        $this->assertEquals(6545484, $disk->used);
        $this->assertEquals("/", $disk->mounted);

        foreach ($disks as $d) {
            $this->assertStringNotContainsString("tmpfs", $d->filesystem);
            $this->assertStringNotContainsString("udev", $d->filesystem);
        }
    }

    public function testEmptyInput()
    {
        $this->assertCount(0, (new Inodes())->parse(""));
    }

    public function testUsedPercent()
    {
        $disk = new InodesDisk();
        $disk->inodes = 1000;
        $disk->used = 500;

        $this->assertEquals(50, $disk->usedPercent());
    }

    public function testStatusThresholds()
    {
        $ok = new InodesDisk();
        $ok->inodes = 1000;
        $ok->used = 800;             // 80%
        $this->assertEquals(Status::ok(), $ok->status());

        $warning = new InodesDisk();
        $warning->inodes = 1000;
        $warning->used = 850;        // 85%
        $this->assertEquals(Status::warning(), $warning->status());

        $error = new InodesDisk();
        $error->inodes = 1000;
        $error->used = 960;          // 96%
        $this->assertEquals(Status::error(), $error->status());
    }
}
