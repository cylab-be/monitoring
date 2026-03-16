<?php

namespace Tests\Unit;

use App\Sensor\DiskActivity;

use Tests\TestCase;

/**
 * Description of SensorsTest
 *
 * @author tibo
 */
class DiskActivityTest extends TestCase
{
    public function testDiskActivity()
    {
        $string = file_get_contents(__DIR__ . "/iostat");
        $sensor = new DiskActivity();
        $values = $sensor->extractUtilValuesFrom2Tables($string);
        $this->assertEquals(5.65, $values["sdb"]);
    }
    
    public function testDiskActivityFreeBSD()
    {
        $string = file_get_contents(__DIR__ . "/iostat-freebsd");
        $sensor = new DiskActivity();
        $values = $sensor->extractUtilValuesFrom2Tables($string);
        $this->assertEquals(3, $values["ada0"]);
    }
}
