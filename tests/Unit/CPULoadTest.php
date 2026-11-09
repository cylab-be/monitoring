<?php

namespace Tests\Unit;

use App\Record;
use App\Sensor\Dataset;
use App\Sensor\Point;
use App\Sensor\Linux\CPULoad;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;

use Tests\TestCase;

/**
 * Description of LoadAvgTest
 *
 * @author tibo
 */
class CPULoadTest extends TestCase
{
    private function record(int $time, string $data) : Record
    {
        $record = new Record();
        $record->time = $time;
        $record->data = $data;
        return $record;
    }

    public function testParseSampleFile()
    {
        $string = file_get_contents(__DIR__ . "/CPULoadTest.txt");
        $this->assertEquals(0.87, (new CPULoad())->parse($string));
    }

    public function testParse()
    {
        $sensor = new CPULoad();

        $this->assertEquals(0.87, $sensor->parse("0.87 1.06 1.01 1/1450 39621"));
        $this->assertEquals(10.5, $sensor->parse("10.50 1.06 1.01 1/1450 39621"));
    }

    public function testLoadPoints()
    {
        $records = DatabaseCollection::make([
            $this->record(1700000000, "0.87 1.06 1.01"),
            $this->record(1700003600, "2.50 1.06 1.01"),
        ]);

        $dataset = (new CPULoad())->loadPoints($records);

        $this->assertInstanceOf(Dataset::class, $dataset);
        $this->assertEquals("Load", $dataset->label);
        $this->assertCount(2, $dataset->data);

        $this->assertInstanceOf(Point::class, $dataset->data[0]);
        $this->assertEquals(1700000000000, $dataset->data[0]->t);
        $this->assertEquals(0.87, $dataset->data[0]->y);
        $this->assertEquals(2.50, $dataset->data[1]->y);
    }
}
