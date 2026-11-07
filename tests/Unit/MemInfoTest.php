<?php

namespace Tests\Unit;

use App\Record;
use App\Sensor\Dataset;
use App\Sensor\MemInfo;
use App\Sensor\Point;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;

use Tests\TestCase;

/**
 * Description of MemInfoTest
 *
 * @author tibo
 */
class MemInfoTest extends TestCase
{
    private function record(int $time, string $data) : Record
    {
        $record = new Record();
        $record->time = $time;
        $record->data = $data;
        return $record;
    }

    private function sampleMeminfo() : string
    {
        return file_get_contents(__DIR__ . "/meminfo");
    }

    public function testParseMeminfo()
    {
        $meminfo = (new MemInfo())->parseMeminfo($this->sampleMeminfo());

        $this->assertEquals(15954328, $meminfo->total);
        $this->assertEquals(1749584, $meminfo->free);
        $this->assertEquals(7732412, $meminfo->cached);
        // used = total - free - cached
        $this->assertEquals(6472332, $meminfo->used());
        $this->assertEqualsWithDelta(0.4057, $meminfo->usedRatio(), 0.001);
    }

    public function testUsedMemoryPoints()
    {
        $records = DatabaseCollection::make([
            $this->record(1700000000, $this->sampleMeminfo()),
            $this->record(1700003600, $this->sampleMeminfo()),
        ]);

        $dataset = (new MemInfo())->usedMemoryPoints($records);

        $this->assertInstanceOf(Dataset::class, $dataset);
        $this->assertEquals("Used", $dataset->label);
        $this->assertCount(2, $dataset->data);

        $point = $dataset->data[0];
        $this->assertInstanceOf(Point::class, $point);
        // point time is in milliseconds
        $this->assertEquals(1700000000000, $point->t);
        $this->assertEqualsWithDelta(6472.332, $point->y, 0.001);
    }

    public function testCachedMemoryPoints()
    {
        $records = DatabaseCollection::make([
            $this->record(1700000000, $this->sampleMeminfo()),
        ]);

        $dataset = (new MemInfo())->cachedMemoryPoints($records);

        $this->assertEquals("Cached", $dataset->label);
        $this->assertCount(1, $dataset->data);
        $this->assertEqualsWithDelta(7732.412, $dataset->data[0]->y, 0.001);
    }

    public function testConfig()
    {
        $config = (new MemInfo())->config();

        $this->assertEquals("memory", $config->label);
        $this->assertEquals("memory", $config->trigger_label);
    }
}
