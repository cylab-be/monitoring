<?php

namespace App\Http\Controllers;

use App\Server;
use App\Record;

use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function echo(Request $request, Server $server)
    {
        if ($server->token !== $request->get("token", "")) {
            abort(403);
        }

        foreach ($request->all() as $label => $data) {
            if (is_null($data)) {
                continue;
            }

            $record = new Record();
            $record->server_id = $server->id;
            $record->time = time();
            $record->label = $label;
            $record->data = $data;
            $record->save();
        }

        return "ok";
    }
}
