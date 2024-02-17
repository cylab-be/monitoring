<?php

namespace Tests\Unit;

use App\User;
use App\Organization;
use App\Server;
use App\Record;
use App\Status;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    public function testClassInstance()
    {
        $class = Server::class;
        $server = new $class;
        $this->assertEquals("App\Server", get_class($server));
    }

    public function testRelations()
    {
        $user = new User();
        $user->name = "test";
        $user->email = "test@example.com";
        $user->password = "abc123";
        $user->save();

        $organization = new Organization();
        $organization->name = "Org";
        $organization->save();

        $organization->users()->save($user);

        $this->assertEquals(
            "Org",
            $user->organizations()->first()->name
        );
    }
    
    /**
     * @group status
     */
    public function testStatusComparison()
    {
        $warning = Status::warning();
        $error = Status::error();
        
        $this->assertGreaterThan($warning, $error);
        $this->assertTrue($error > $warning);
        
        $this->assertEquals(Status::error(), max($warning, $error));
    }


    /**
     * @group status-change
     */
    public function testStatusChangeDetection()
    {
        $organization = new Organization();
        $organization->name = "ACME";
        $organization->save();

        $server = new \App\Server();
        $server->name = "My test server";
        $server->organization()->associate($organization);
        $server->save();

        $server_id = $server->id;
        
        $record = new Record();
        $record->server_id = $server_id;
        $record->time = time();
        $record->data = "";
        $record->save();
        
        $record_id = $record->id;

        $user = new User();
        $user->name = "Test";
        $user->email = "thibault.debatty@gmail.com";
        $user->password = "qmlskdj";
        $user->save();
        $organization->users()->attach($user->id);

        // Insert a fake status change
        $change = new \App\StatusChange();
        $change->status = \App\Status::ERROR;
        $change->time = time();
        $change->server_id = $server_id;
        $change->record_id = $record_id;
        $change->save();

        // Check if a new StatusChange was correctly saved
        $last_change = $server->lastChange();
        $this->assertEquals(
            $change->status,
            $last_change->status
        );
        
        // Run change detection
        //$change_detection_job = new \App\Jobs\StatusChangeDetection();
        //$change_detection_job->detectChangeForServer($server);


        // Insert multiple status changes to simulate bouncing
        for ($i = 0; $i < 4; $i++) {
            $change = new \App\StatusChange();
            $change->status = 155;
            $change->server_id = $server_id;
            $change->time = time() + $i;
            $change->record_id = $record_id;
            $change->save();

            // Run change detection
            $change_detection_job = new \App\Jobs\StatusChangeDetection();
            $change_detection_job->detectChangeForServer($server);
        }
    }
}
