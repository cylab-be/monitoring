<?php

namespace Tests\Unit;

use App\AgentScheduler;

use Tests\TestCase;

/**
 * Description of SensorManagementTest
 *
 * @author tibo
 */
class SensorManagementTest extends TestCase
{
    public function testAutodiscover()
    {
        $manager = AgentScheduler::get();
        $sensors = $manager->sensors();
        
        $this->assertTrue($sensors->count() > 5);
    }
}
