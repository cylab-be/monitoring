<?php

namespace App\Http\Controllers;

use App\Record;

class RecordController extends Controller
{
    public function show(Record $record)
    {
        return view("record.show")->with(["record" => $record]);
    }
}
