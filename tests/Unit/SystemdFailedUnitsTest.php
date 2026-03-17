<?php

namespace Tests\Unit;

use App\Sensor\SystemdFailedUnits;

use Tests\TestCase;

/**
 * Description of SensorsTest
 *
 * @author tibo
 */
class SystemdFailedUnitsTest extends TestCase
{
    public function testOneFailedUnit()
    {
        $string = file_get_contents(__DIR__ . "/systemd-failed-units-one");
        $sensor = new SystemdFailedUnits();
        $units = $sensor->parse($string);
        $this->assertEquals(1, count($units));
    }
    
    public function testZeroFailedUnit()
    {
        $string = file_get_contents(__DIR__ . "/systemd-failed-units-zero");
        $sensor = new SystemdFailedUnits();
        $units = $sensor->parse($string);
        $this->assertEquals(0, count($units));
    }
    
    public function testTwoFailedUnits()
    {
        $string = file_get_contents(__DIR__ . "/systemd-failed-units-two");
        $sensor = new SystemdFailedUnits();
        $units = $sensor->parse($string);
        $this->assertEquals(2, count($units));
    }
}
