<?php

namespace Tests\Feature;

use App\Organization;
use App\Server;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

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

        $data = json_decode(file_get_contents(__DIR__ . "/ping.json"), true);
        $data["token"] = $server->token;

        $this->post('/api/record/' . $server->id, $data)->assertStatus(200);
    }
}
