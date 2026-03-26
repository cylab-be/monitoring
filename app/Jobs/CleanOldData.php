<?php

namespace App\Jobs;

use App\Record;
use App\Report;
use App\ReportSummary;
use App\FailedJob;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanOldData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // retentiont, in seconds
    // 1 week
    const RETENTION = 3600 * 24 * 7;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $max_age = time() - self::RETENTION;
        
        $count = Record::where("time", "<", $max_age)->delete();
        logger()->info("Deleted $count records");
        
        $count = Report::where("time", "<", $max_age)->delete();
        logger()->info("Deleted $count reports");
        
        $count = ReportSummary::where("time", "<", $max_age)->delete();
        logger()->info("Deleted $count report summaries");
        
        $count = FailedJob::where("failed_at", "<", Carbon::createFromTimestamp($max_age))->delete();
        logger()->info("Deleted $count failed jobs records");
    }
}
