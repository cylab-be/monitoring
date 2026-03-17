<?php

namespace Tests\Unit;

use App\Sensor\DockerRestarts;

use Tests\TestCase;

/**
 * Description of SensorsTest
 *
 * @author tibo
 */
class DockerRestartsTest extends TestCase
{
    public function testDockerRestarts()
    {
        $string = file_get_contents(__DIR__ . "/docker-restarts");
        $sensor = new DockerRestarts();
        $restarts = $sensor->parse($string);
        $this->assertEquals(8, count($restarts));
        $this->assertEquals(3, $restarts["/monitoring-mysql-1"]);
    }
}
