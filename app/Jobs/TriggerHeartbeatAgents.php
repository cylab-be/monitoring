<?php

namespace App\Jobs;

use App\Server;
use App\Sensor\Heartbeat;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Heartbeat Agents are triggered on a time interval.
 * See App\Console\Kernel
 */
class TriggerHeartbeatAgents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (Server::all() as $server) {
            /** @var Server $server */
            $last_record = $server->records()->orderByDesc("time")->first();
            if ($last_record == null) {
                continue;
            }
            
            $agent = new Heartbeat();
            RunAgent::dispatch($agent, $last_record);
        }
    }
}
