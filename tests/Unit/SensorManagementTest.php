<?php

namespace Tests\Unit;

use App\AgentScheduler;

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
        $manager = AgentScheduler::get();
        $sensors = $manager->autodiscover();
        
        $this->assertTrue($sensors->count() > 5);
    }
}
