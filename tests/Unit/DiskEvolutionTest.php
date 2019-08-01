<?php

namespace Tests\Unit;

use App\Sensor\DiskEvolution;
use App\Sensor\Partition;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Description of DiskEvolutionTest
 *
 * @group diskevolution
 * @author tibo
 */

class DiskEvolutionTest extends TestCase
{

    use RefreshDatabase;



    public function testSlowEvolution()
    {
        $p = new Partition();
        $p->filesystem = "test";
        $p->blocks = 20;
        $p->used = 10;
        $oldPartitions = array($p);

        $p = new Partition();
        $p->filesystem = "test";
        $p->blocks = 20;
        $p->used = 11;
        $newPartitions = array($p);

        $newAndOld = array($newPartitions, $oldPartitions);


        $sensor = new DiskEvolution(new \App\Server());
        $partitions = $sensor->computeEvolution($newAndOld, 24);

        // test the result is correct...
        $this->assertEquals(1, $partitions[0]->delta);
        $this->assertEquals("test", $partitions[0]->filesystem);
        $this->assertEquals(216, $partitions[0]->timeUntillFull);
        $this->assertEquals(0, $sensor->computeStatusFromDeltas($partitions));
    }

    public function testQuickFull()
    {
        $p = new Partition();
        $p->filesystem = "test";
        $p->blocks = 20;
        $p->used = 10;
        $oldPartitions = array($p);

        $p = new Partition();
        $p->filesystem = "test";
        $p->blocks = 20;
        $p->used = 15;
        $newPartitions = array($p);

        $newAndOld = array($newPartitions, $oldPartitions);


        $sensor = new DiskEvolution(new \App\Server());
        $partitions = $sensor->computeEvolution($newAndOld, 24);

        // test the result is correct...
        $this->assertEquals(5, $partitions[0]->delta);
        $this->assertEquals("test", $partitions[0]->filesystem);
        $this->assertEquals(24, $partitions[0]->timeUntillFull);
        $this->assertEquals(10, $sensor->computeStatusFromDeltas($partitions));
    }
}
