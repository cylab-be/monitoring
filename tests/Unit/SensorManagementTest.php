<?php

namespace Tests\Unit;

use App\SensorHandler;

use Tests\TestCase;

/**
 * Description of SensorManagementTest
 *
 * @group sensor-manager
 * @author tibo
 */
class SensorManagementTest extends TestCase
{
    public function testAutodiscover()
    {
        $manager = SensorHandler::get();
        var_dump($manager->autodiscover());
    }
}
