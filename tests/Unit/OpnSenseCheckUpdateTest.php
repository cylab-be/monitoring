<?php

namespace Tests\Unit;

use App\Sensor\FreeBSD\OpnSenseCheckUpdate;

use Tests\TestCase;

class OpnSenseCheckUpdateTest extends TestCase
{
    public function testParse()
    {
        $data = file_get_contents(__DIR__ . "/OpnSenseCheckUpdate");
        $sensor = new OpnSenseCheckUpdate;
        $updates = $sensor->parse($data);
        
        $this->assertEquals(1, $updates["install"]);
        $this->assertEquals(84, $updates["upgrade"]);
    }
}
