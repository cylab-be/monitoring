<?php

namespace Tests\Unit;

use App\Record;
use App\Sensor\Netstat;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;

use Tests\TestCase;

/**
 * Description of NetstatTest
 *
 * @author tibo
 */
class NetstatTest extends TestCase
{
    private function record(int $time, string $data) : Record
    {
        $record = new Record();
        $record->time = $time;
        $record->data = $data;
        return $record;
    }

    public function testFirstPointHasZeroRatio()
    {
        $records = DatabaseCollection::make([
            $this->record(1700000000, file_get_contents(__DIR__ . "/netstat")),
        ]);

        $dataset = (new Netstat())->points($records);

        $this->assertCount(1, $dataset->data);
        $this->assertEquals(0, $dataset->data[0]->y);
    }

    public function testRatioBetweenTwoReports()
    {
        $records = DatabaseCollection::make([
            $this->record(1700000000, file_get_contents(__DIR__ . "/netstat")),
            $this->record(1700003600, file_get_contents(__DIR__ . "/netstat-mint")),
        ]);

        $dataset = (new Netstat())->points($records);

        $this->assertCount(2, $dataset->data);

        // first point : no previous report, ratio is 0
        $this->assertEquals(0, $dataset->data[0]->y);
        $this->assertEquals(1700000000000, $dataset->data[0]->t);

        // second point : (50555 - 24004) / (6161477 - 5966113) * 100
        $expected = (50555 - 24004) / (6161477 - 5966113) * 100;
        $this->assertEqualsWithDelta($expected, $dataset->data[1]->y, 0.01);
        $this->assertEquals(1700003600000, $dataset->data[1]->t);
    }

    public function testZeroSentSegmentsDoesNotDivideByZero()
    {
        $data = file_get_contents(__DIR__ . "/netstat");
        $records = DatabaseCollection::make([
            $this->record(1700000000, $data),
            $this->record(1700003600, $data),
        ]);

        $dataset = (new Netstat())->points($records);

        $this->assertCount(2, $dataset->data);
        $this->assertEquals(0, $dataset->data[1]->y);
    }
}
