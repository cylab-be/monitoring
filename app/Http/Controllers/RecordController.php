<?php

namespace App\Http\Controllers;

use App\Record;
use App\AgentScheduler;

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

        // find matching agents
        $scheduler = AgentScheduler::get();
        $agents = $scheduler->agentsForLabel($record->label);

        return view("record.show", [
            "record" => $record,
            "agents" => $agents,
            "organization" => $record->server->organization]);
    }

    /**
     * Run an agent on the specified record.
     * @param Record $record
     * @param string $agent
     */
    public function run(Record $record, string $agent)
    {
        $this->authorize("show", $record->server);

        // find matching agents
        $scheduler = AgentScheduler::get();
        $agent = $scheduler->agent($agent);

        $report = $agent->analyze($record);
        $report->time = time();
        $report->label = $agent->config()->label;
        $report->server()->associate($record->server);
        $report->record()->associate($record);

        return view("report.show", ["record" => $record, "report" => $report]);
    }
}
