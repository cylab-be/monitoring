<?php

namespace App\Jobs;

use App\Sensor;
use App\Record;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;

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
     * Executed in the main process.
     *
     * @return void
     */
    public function __construct(Sensor $agent, Record $record)
    {
        $this->agent = $agent;
        $this->record = $record;

        logger()->info("Dispatch agent " . get_class($agent) .
                " for server #" . $record->server_id);
        logger()->info("Queue size: " . Queue::size());
    }

    /**
     * Execute the job in the background worker.
     *
     * @return void
     */
    public function handle()
    {
        logger()->info("Start agent " . get_class($this->agent) .
                " for server #" . $this->record->server_id);
        $start = microtime(true);

        $record = $this->record;
        $report = $this->agent->analyze($record);

        if (!is_null($report)) {
            $report->time = time();
            $report->server_id = $record->server_id;
            $report->label = $this->agent->config()->label;
            $report->record_id = $record->id;
            $report->save();
        }

        $runtime = round((microtime(true) - $start) * 1000);
        logger()->info("End agent " . get_class($this->agent) . " | Execution time: $runtime ms");
    }
}
