<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Collection;

/**
 * Aggregates the last reports to get a global server status.
 *
 * @property int $server_id
 * @property Server $server
 * @property int $time
 * @property int $status_code
 * @property array $reports
 */
class ReportSummary extends Model
{
    public $timestamps = false;
    
    protected $casts = [
        'reports' => 'array'
    ];
    
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    
    public function status() : Status
    {
        return new Status($this->status_code);
    }
    
    /**
     *
     * @return DatabaseCollection<Report>
     */
    public function reports() : DatabaseCollection
    {
        // this will show errors first, then warnings etc.
        // and sorted by title
        return Report::find($this->reports)
                ->sortBy("title")
                ->sortByDesc("status_code");
    }
    
    /**
     *
     * @param Collection<Report> $reports
     */
    public function setReports(Collection $reports)
    {
        $this->reports = $reports->pluck("id")->toArray();
    }
    
    public function save(array $options = [])
    {
        parent::save($options);
        
        AgentScheduler::get()->notifySummary($this);
        
        return true;
    }
    
    public function time() : Carbon
    {
        return Carbon::createFromTimestamp($this->time);
    }
    
    public static function default(Server $server) : ReportSummary
    {
        $summary = new ReportSummary();
        $summary->server = $server;
        $summary->server_id = $server->id;
        $summary->time = 0;
        $summary->status_code = -1;
        $summary->reports = [];
        
        return $summary;
    }
}
