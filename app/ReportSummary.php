<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
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
     * @return Collection<Report>
     */
    public function reports() : Collection
    {
        return Report::find($this->reports);
    }
    
    /**
     *
     * @param Collection<Report> $reports
     */
    public function setReports(Collection $reports)
    {
        $this->reports = $reports->pluck("id")->toArray();
    }
    
    public function time() : Carbon
    {
        return Carbon::createFromTimestamp($this->time);
    }
}
