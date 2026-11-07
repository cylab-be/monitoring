<?php

namespace Tests\Unit;

use App\Report;
use App\Status;

use Tests\TestCase;

/**
 * Description of ReportTest
 *
 * @author tibo
 */
class ReportTest extends TestCase
{
    public function testDefaultStatusIsUnknown()
    {
        $report = new Report();

        $this->assertEquals(-1, $report->status_code);
        $this->assertEquals(Status::unknown(), $report->status());
    }

    public function testFluentSetters()
    {
        $report = new Report();

        $ret = $report
            ->setTitle("CPU : Load")
            ->setStatus(Status::warning())
            ->setHTML("<p>hello</p>");

        // all setters must return the same instance
        $this->assertSame($report, $ret);

        $this->assertEquals("CPU : Load", $report->title());
        $this->assertEquals(10, $report->status_code);
        $this->assertEquals("WARNING", (string) $report->status());
    }

    public function testHtmlIsCompressedAndDecompressedTransparently()
    {
        $report = new Report();
        $html = "<p>Some HTML</p>";

        $report->setHTML($html);

        // the raw attribute must be compressed...
        $this->assertNotEquals($html, $report->getAttributes()['html']);

        // ...and transparently decompressed by the accessor
        $this->assertEquals($html, $report->html);
    }

    public function testTimeFormat()
    {
        $report = new Report();
        $report->time = 1700000000;

        $this->assertEquals(date("Y-m-d H:i:s", 1700000000), $report->time());
    }
}
