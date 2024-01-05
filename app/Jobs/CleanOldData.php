<?php

namespace App\Jobs;

use App\Record;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanOldData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // retentiont, in seconds
    // 2 weeks
    const RETENTION = 3600 * 24 * 14;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = Record::where("time", "<", time() - self::RETENTION)->delete();
        Log::info("Deleted $count records");
    }
}
