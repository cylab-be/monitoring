<?php

namespace App\Jobs;

use App\Notification;
use App\Organization;
use App\Server;
use App\StatusChange;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class StatusChangeDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::notice("Check for status changes...");
        foreach (Organization::all() as $organization) {
            /* @var $organization \App\Organization */
            foreach ($organization->servers as $server) {
                $this->detectChangeForServer($server);
            }
        }
    }

    public function detectChangeForServer(Server $server)
    {
        $last_change = $server->lastChange();
        $current_status = $server->status();
        
        // probably a new server:
        // never been a change detection before
        if ($last_change === null) {
            $change = new StatusChange();
            $change->server_id = $server->id;
            $change->time = time();
            $change->status = $current_status->code();
            $change->record_id = $server->lastRecord()->id;
            $change->save();
            return;
        }

        if ($last_change->status == $current_status->code()) {
            // no change
            return;
        }
        
        Log::notice("Status of server changed to " . $current_status->code() . " for server #" . $server->id);

        $change = new StatusChange();
        $change->server_id = $server->id;
        $change->time = time();
        $change->status = $current_status->code();
        $change->record_id = $server->lastRecord()->id;
        $change->save();

        $this->sendNotificationIfRequired($change);
    }

    /**
     * Maximum number of notifications sent per day.
     */
    const NOTIFICATIONS_PER_DAY = 4;

    public function sendNotificationIfRequired(StatusChange $change)
    {
        // Only send a notification if server changes to status "error"
        if ($change->status < \App\Status::ERROR) {
            return;
        }
        
        $server = $change->server();
        $server_id = $server->id;

        $onedayago = time() - 24 * 3600;
        $sent_notifications_count = Notification::findForServer($server_id, $onedayago)->count();

        if ($sent_notifications_count < self::NOTIFICATIONS_PER_DAY) {
            $notification = new Notification();
            $notification->server()->associate($server);
            $notification->type = "change";
            $notification->change_id = $change->id;
            $notification->saveAndSend();

            return;
        }

        if ($sent_notifications_count == self::NOTIFICATIONS_PER_DAY) {
            $notification = new Notification();
            $notification->server()->associate($server);
            $notification->type = "bouncing";
            $notification->change_id = $change->id;
            $notification->saveAndSend();

            return;
        }

        // nothing to do if number of sent notifications > COUNT
    }
}
