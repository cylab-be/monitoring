<?php

namespace Tests\Unit;

use App\Record;
use App\Sensor\Dataset;
use App\Sensor\Point;
use App\Sensor\Linux\CPUPressure;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;

use Tests\TestCase;

/**
 * Description of CPUPressureTest
 *
 * @author tibo
 */
class CPUPressureTest extends TestCase
{
    private function record(int $time, string $data) : Record
    {
        $record = new Record();
        $record->time = $time;
        $record->data = $data;
        return $record;
    }

    private function psiRecord(int $time, float $avg300) : Record
    {
        $data = "some avg10=1.00 avg60=2.00 avg300=" . $avg300 . " total=1\n";
        $data .= "full avg10=0.00 avg60=0.00 avg300=0.00 total=0\n";
        return $this->record($time, $data);
    }

    public function testParseSampleFile()
    {
        $string = file_get_contents(__DIR__ . "/CPUPressureTest.txt");
        $this->assertEquals(12.34, (new CPUPressure())->parse($string));
    }

    public function testParse()
    {
        $sensor = new CPUPressure();

        $psi = "some avg10=5.25 avg60=8.10 avg300=12.34 total=123456\n";
        $psi .= "full avg10=0.00 avg60=0.00 avg300=0.00 total=0\n";

        // "some" line takes priority
        $this->assertEquals(12.34, $sensor->parse($psi));

        $psi = "some avg10=10.00 avg60=20.00 avg300=30.50 total=999\n";
        $psi .= "full avg10=1.00 avg60=2.00 avg300=3.00 total=1\n";
        $this->assertEquals(30.5, $sensor->parse($psi));

        // No "some" line: no match -> 0
        $this->assertEquals(0.0, $sensor->parse("full only"));

        // Garbage / empty input
        $this->assertEquals(0.0, $sensor->parse(""));
        $this->assertEquals(0.0, $sensor->parse("this is not psi output"));
    }

    public function testPressurePoints()
    {
        $records = DatabaseCollection::make([
            $this->psiRecord(1700000000, 3.25),
            $this->psiRecord(1700003600, 15.50),
        ]);

        $dataset = (new CPUPressure())->pressurePoints($records);

        $this->assertInstanceOf(Dataset::class, $dataset);
        $this->assertEquals("CPU pressure", $dataset->label);
        $this->assertCount(2, $dataset->data);

        $this->assertInstanceOf(Point::class, $dataset->data[0]);
        $this->assertEquals(1700000000000, $dataset->data[0]->t);
        $this->assertEquals(3.25, $dataset->data[0]->y);
        $this->assertEquals(15.50, $dataset->data[1]->y);
    }
}
