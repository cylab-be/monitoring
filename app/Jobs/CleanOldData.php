<?php

namespace App\Jobs;

use App\Record;
use App\Report;
use App\ReportSummary;

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
        $max_age = time() - self::RETENTION;
        
        $count = Record::where("time", "<", $max_age)->delete();
        Log::info("Deleted $count records");
        
        $count = Report::where("time", "<", $max_age)->delete();
        Log::info("Deleted $count reports");
        
        $count = ReportSummary::where("time", "<", $max_age)->delete();
        Log::info("Deleted $count report summaries");
    }
}
