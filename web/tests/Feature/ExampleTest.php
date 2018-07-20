<?php

namespace Tests\Feature;

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
        $server = new Server();
        $server->name = "srv01";
        $server->save();

        $this->post('/api/record/' . $server->id, [])->assertStatus(403);
        $this->post('/api/record/' . $server->id, ["token" => "abc123"])->assertStatus(403);

        $data = [
            "token" => $server->token,
            "version" => "0.1.2",
            "uname" => "Linux whatever..."
        ];

        $this->post('/api/record/' . $server->id, $data)->assertStatus(200);
    }
}
