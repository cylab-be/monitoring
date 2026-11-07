<?php

namespace Tests\Unit;

use App\Sensor\Linux\ArpEntry;
use App\Sensor\Linux\Arpwatch;

use Tests\TestCase;

/**
 * Description of ArpwatchTest
 *
 * @author tibo
 */
class ArpwatchTest extends TestCase
{
    private function parse(string $data): array
    {
        return (new Arpwatch())->parse($data);
    }

    public function testParseSampleFile()
    {
        $string = file_get_contents(__DIR__ . "/arpwatch");
        $entries = $this->parse($string);

        $this->assertCount(4, $entries);
        $this->assertInstanceOf(ArpEntry::class, $entries[0]);

        $this->assertEquals("1c:69:7a:69:8d:82", $entries[0]->mac);
        $this->assertEquals("192.168.178.2", $entries[0]->ip);
        $this->assertEquals(1774096272, $entries[0]->timestamp);
        $this->assertEquals("wlp194s0", $entries[0]->interface);

        $this->assertEquals("48:45:e6:b2:79:7b", $entries[3]->mac);
        $this->assertEquals("192.168.178.180", $entries[3]->ip);
        $this->assertEquals(1774096272, $entries[3]->timestamp);

        // The seenAt() convenience method must be consistent with the raw timestamp.
        $this->assertEquals("1774096285", $entries[1]->seenAt()->format("U"));
    }

    public function testIgnoresBlankLinesAndComments()
    {
        $string = "# arpwatch sample\n"
            . "\n"
            . "   # indented comment\n"
            . "1c:69:7a:69:8d:82\t192.168.178.2\t1774096272\twlp194s0\n";
        $entries = $this->parse($string);

        $this->assertCount(1, $entries);
        $this->assertEquals("1c:69:7a:69:8d:82", $entries[0]->mac);
    }

    public function testEmptyInput()
    {
        $this->assertEquals([], $this->parse(""));
        $this->assertEquals([], $this->parse("\n  \n"));
    }

    public function testDedupesKeepingMostRecent()
    {
        $string = "1c:69:7a:69:8d:82 192.168.178.2 1774096200 wlp194s0\n"
            . "1c:69:7a:69:8d:82 192.168.178.2 1774096272 wlp194s0\n"
            . "1c:69:7a:69:8d:82 192.168.178.2 1774096100 wlp194s0\n";
        $entries = $this->parse($string);

        $this->assertCount(1, $entries);
        $this->assertEquals(1774096272, $entries[0]->timestamp);
    }

    public function testKeepsDifferentMacForSameIp()
    {
        $string = "1c:69:7a:69:8d:82 192.168.178.2 1774096200 wlp194s0\n"
            . "c8:2e:18:dc:87:4c 192.168.178.2 1774096272 wlp194s0\n";
        $entries = $this->parse($string);

        $this->assertCount(2, $entries);
    }
}
