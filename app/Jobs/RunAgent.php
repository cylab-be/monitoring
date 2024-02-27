<?php

namespace App\Jobs;

use App\Sensor;
use App\Record;

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
     * @var Record
     */
    public $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sensor $agent, Record $record)
    {
        $this->agent = $agent;
        $this->record = $record;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $label = $this->agent->config()->label;
        logger()->info("Start agent $label for server #" . $this->record->server_id);
        $start = microtime(true);
        
        $trigger_label = $this->agent->config()->trigger_label;
        $record = $this->record;
        $server = $this->record->server;
        
        $records = $server->lastRecords($trigger_label);
        
        $report = $this->agent->analyze($records, $server->info());
        $report->time = time();
        $report->server_id = $server->id;
        $report->label = $this->agent->config()->label;
        $report->record_id = $record->id;
        $report->save();
        
        $runtime = round((microtime(true) - $start) * 1000);
        logger()->info("End agent $label | Execution time: $runtime ms");
    }
}
