<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Server;
use App\Record;
use App\Key;

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
    
    /**
     * https://github.com/givebutter/laravel-keyable/blob/master/src/Http/Middleware/AuthenticateApiKey.php
     * @param Request $request
     */
    public function inventory(Request $request)
    {
        $organization = $this->getOrganizationFromRequest($request);
        return response()->json($organization->inventory());
    }
    
    public function dashboard(Request $request)
    {
        $organization = $this->getOrganizationFromRequest($request);
        return response()->json($organization->dashboard());
    }

    private function getOrganizationFromRequest($request) : Organization
    {
        $token = $request->bearerToken();
        //Check for presence of key
        if (! $token) {
            abort(401);
        }

        $key = Key::getByPlaintextKey($token);
        if (! ($key instanceof Key)) {
            abort(401);
        }
        
        $key->use();
        
        return $key->organization;
    }
}
