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
        $this->get('/')->assertResponseOk();
    }

    public function testPing()
    {
        $organization = new Organization();
        $organization->name = "TEST";
        $organization->save();

        $server = new Server();
        $server->name = "srv01";
        $organization->servers()->save($server);

        $this->post('/api/record/' . $server->id, [])->assertResponseStatus(403);
        $this->post('/api/record/' . $server->id, ["token" => "abc123"])->assertResponseStatus(403);

        $data = json_decode(file_get_contents(__DIR__ . "/ping.json"), true);
        $data["token"] = $server->token;

        $this->post('/api/record/' . $server->id, $data)->assertResponseOk();
    }
    
    public function testRegisterAndManageServers()
    {
        $this->visit("/register")
                ->type("dummy", "name")
                ->type("dummy@example.com", "email")
                ->type("abc123!", "password")
                ->type("abc123!", "password_confirmation")
                ->press("Register")
                ->assertResponseOk()
                // on the "Organizations" page
                ->see("dummy")
                ->click("dummy")
                // add a server
                ->click("New server")
                ->type("srv01", "name")
                ->press("Save")
                ->assertResponseOk();
                
    }
}
