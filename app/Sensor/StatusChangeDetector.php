<?php

namespace App\Sensor;

use App\ReportSummary;
use App\StatusChange;

/**
 * Description of StatusChangeDetector
 *
 * @author tibo
 */
class StatusChangeDetector
{
    public function analyze(ReportSummary $summary)
    {
        $current_status = $summary->status();
        $server = $summary->server;
        
        logger()->info("Start agent status change detector for server #" . $server->id);
        
        $last_change = $server->lastChange();
        
        // probably a new server:
        // never been a change detection before
        if ($last_change === null) {
            $change = new StatusChange();
            $change->server()->associate($server);
            $change->time = time();
            $change->status = $current_status->code();
            $change->save();
            return;
        }
        
        if ($last_change->status == $current_status->code()) {
            // no change
            return;
        }
        
        $change = new StatusChange();
        $change->server()->associate($server);
        $change->time = time();
        $change->status = $current_status->code();
        $change->save();
    }
}
