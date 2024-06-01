<?php

namespace App\Http\Controllers;

use App\Record;

class RecordController extends Controller
{
    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }
    
    public function show(Record $record)
    {
        $this->authorize("show", $record->server);
        return view("record.show")->with(["record" => $record]);
    }
}
