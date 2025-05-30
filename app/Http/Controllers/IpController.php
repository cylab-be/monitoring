<?php

namespace App\Http\Controllers;

use App\Server;
use App\Ip;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class IpController extends Controller
{
    public function create(Server $server)
    {
        $this->authorize("save", $server);
        $ip = new Ip();
        $ip->server()->associate($server);
        return view("ip.edit", ["ip" => $ip]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "ip" => "required|ip",
            "comment" => "nullable|string|max:255",
            "server_id" => Rule::in(Auth::user()->serverIds()),
        ]);

        $ip = new Ip();
        $ip->ip = $request->get("ip");
        $ip->comment = $request->get("comment", null);
        $ip->server()->associate(Server::find($request->get("server_id")));

        $this->authorize("save", $ip->server);
        $ip->save();

        return redirect($ip->server->getUrlAttribute());
    }

    public function destroy(Ip $ip)
    {
        $this->authorize("save", $ip->server);
        $ip->delete();
        return redirect($ip->server->getUrlAttribute());
    }
}
