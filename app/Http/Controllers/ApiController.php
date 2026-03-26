<?php

namespace App\Http\Controllers;

use App\Server;
use App\Record;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function echo(Request $request, Server $server)
    {
        if ($server->token !== $request->get("token", "")) {
            abort(403);
        }

        $now = time();
        // Wrap the code in a transaction to decrease concurrent writes to the database.
        DB::transaction(function () use ($request, $server, $now) {
            foreach ($request->all() as $label => $data) {
                // 'token' is used for auth only; do not store it as a metric record.
                if ($label === 'token') {
                    continue;
                }

                if (is_null($data)) {
                    continue;
                }

                $record = new Record();
                $record->server_id = $server->id;
                $record->time = $now;
                $record->label = $label;
                $record->data = $data;
                $record->save();
            }
        });

        return "ok";
    }
}
