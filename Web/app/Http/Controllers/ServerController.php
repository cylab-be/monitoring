<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizations;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{

    public function register(Request $request){
        $token_id = str_random(128);
        $server = new Server();
        $server->name = "New Server";
        $server->token = $token_id;
        $server->save();
        return $token_id;
    }
}
