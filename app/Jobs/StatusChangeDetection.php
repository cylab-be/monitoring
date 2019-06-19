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
use Illuminate\Support\Facades\Mail;

class StatusChangeDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (Organization::all() as $organization) {
            /* @var $organization \App\Organization */
            foreach ($organization->servers as $server) {
                $this->detectChangeForServer($server);
            }
        }
    }

    public function detectChangeForServer(Server $server)
    {
        $last_change = StatusChange::getLastChangeForServer($server->id);
        $current_status = $server->status();

        if ($last_change->status == $current_status) {
            // no change
            return;
        }

        $change = new StatusChange();
        $change->server_id = $server->id;
        $change->time = time();
        $change->status = $current_status;
        $change->save();

        $this->sendNotificationIfRequired($change);
    }

    public function sendNotificationIfRequired(StatusChange $change)
    {
        $server = $change->server();

        $notification = new Notification();
        $notification->server()->associate($server);
        $notification->type = "change";
        $notification->change_id = $change->id;
        $notification->save();

        foreach ($server->organization->users as $user) {
            Mail::to($user)->send(new \App\Mail\StatusChanged($change));
        }
    }
}
