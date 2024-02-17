<?php

namespace App\Jobs;

use App\Sensor;
use App\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunAgent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * 
     * @var Sensor
     */
    public $agent;
    
    
    /**
     * 
     * @var Server
     */
    public $server;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sensor $agent, Server $server)
    {
        $this->agent = $agent;
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $trigger_label = $this->agent->config()->trigger_label;
        $records = $this->server->lastRecords($trigger_label);
        
        /** @var Sensor $agent */
        $report = $this->agent->analyze($records, $this->server->info());
        $report->time = time();
        $report->server_id = $this->server->id;
        $report->label = $this->agent->config()->label;
        $report->save();
    }
}
