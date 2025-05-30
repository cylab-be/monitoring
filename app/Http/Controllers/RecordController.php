<?php

namespace App\Http\Controllers;

use App\Record;
use App\Organization;

class RecordController extends Controller
{
    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    public function show(Organization $organization, Record $record)
    {
        $this->authorize("show", $record->server);
        return view("record.show")->with(["record" => $record]);
    }
}
