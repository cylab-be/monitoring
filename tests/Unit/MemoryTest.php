<?php

namespace Tests\Unit;

use App\Sensor\MemoryTypes;

use Tests\TestCase;

/**
 * Description of MemoryTest
 *
 * @group memory
 * @author tibo
 */
class MemoryTest extends TestCase
{
    /**
     * @group ifconfig
     * @group sensors
     */
    public function testMemoryTypes()
    {
        $string = file_get_contents(__DIR__ . "/memory-dmi");
        $sensor = new MemoryTypes();

        var_dump($sensor->parse($string));
    }
}
