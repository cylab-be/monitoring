<?php

namespace Tests\Feature;

use App\Organization;
use App\Server;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->get('/')->assertStatus(200);
    }

    public function testRecord()
    {
        $organization = new Organization();
        $organization->name = "TEST";
        $organization->save();

        $server = new Server();
        $server->name = "srv01";
        $organization->servers()->save($server);

        $this->post('/api/record/' . $server->id, [])->assertStatus(403);
        $this->post('/api/record/' . $server->id, ["token" => "abc123"])->assertStatus(403);

        $data = [
            "token" => $server->token,
            "version" => "0.1.2",
            "uname" => "Linux think 4.15.0-24-generic #26~16.04.1-Ubuntu SMP Fri Jun 15 14:35:08 UTC 2018 x86_64 x86_64 x86_64 GNU/Linux",
            "loadavg" => "0.83 0.87 0.70 2/1747 25404",
            "reboot" => true,
            "updates" => "
6 packages can be updated.
0 updates are security updates.

",
            ];

        $this->post('/api/record/' . $server->id, $data)->assertStatus(200);
    }
}
