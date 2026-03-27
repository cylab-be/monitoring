<?php

namespace Tests\Unit;

use App\Sensor\IpNeighbors;

use Tests\TestCase;

/**
 *
 * @author tibo
 */
class IpNeighborsTest extends TestCase
{
    public function testView()
    {
        $string = file_get_contents(__DIR__ . "/IpNeighbors");
        $sensor = new IpNeighbors();
        $this->assertEquals(
            file_get_contents(__DIR__ . "/IpNeighbors.html"),
            $sensor->parse($string)
        );
    }
}
