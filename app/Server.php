<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Collection;

/**
 * App\Server
 *
 * @property int $id
 * @property int $organization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $token
 * @property string $read_token
 * @property-read \App\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereReadToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    
    protected $fillable = ["token"];
    
    // don't show tokens when serializing to json
    protected $hidden = ['token', 'read_token'];
    
    // add attributes when serializing to json
    // https://laravel.com/docs/8.x/eloquent-serialization#appending-values-to-json
    protected $appends = ['url', 'status', 'failing_sensors', 'last_record_time'];
    
    public function getUrlAttribute() : string
    {
        return action("ServerController@show", ["server" => $this]);
    }
    
    public function getStatusAttribute() : array
    {
        return $this->status()->jsonSerialize();
    }
    
    public function getFailingSensorsAttribute() : array
    {
        $failing_sensors = [];
        
        foreach ($this->getSensorsNOK() as $sensor) {
            $failing_sensors[] = $sensor->name();
        }
        
        return $failing_sensors;
    }
    
    public function getLastRecordTimeAttribute() : int
    {
        return $this->info()->lastRecordTime()->timestamp;
    }
    
    private $info = null;

    public function __construct(array $attributes = array())
    {
        $attributes["token"] = str_random(32);
        parent::__construct($attributes);
    }

    public function organization()
    {
        return $this->belongsTo("App\Organization");
    }
    
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function lastRecord(string $label) : ?Record
    {
        return $this->records()
                ->where("label", $label)
                ->orderBy("time", "desc")->first();
    }

    /**
     * Get the last day of data.
     */
    public function lastRecords(string $label) : DatabaseCollection
    {
        $start = time() - 24 * 3600;
        return $this->records()
                ->where("label", $label)
                ->where("time", ">", $start)
                ->orderBy("time")
                ->get();
    }

    public function hasData() : bool
    {
        return true;
        //return false;
        //return $this->lastRecord() != null;
    }

    public function info() : ServerInfo
    {
        if (is_null($this->info)) {
            $this->info = new ServerInfo($this);
        }

        return $this->info;
    }

    /**
     *
     * @return \App\Status
     */
    public function status() : Status
    {
        return Status::max($this->lastReports());
    }

    public function getSensorsNOK() : array
    {
        $sensorsNOK = [];
        foreach ($this->reports() as $sensor) {
            if ($sensor->status()->code() > 0) {
                $sensorsNOK[] = $sensor;
            }
        }
        return $sensorsNOK;
    }
    
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    
    /**
     * Get the last report for each label.
     */
    public function lastReports()
    {
        $reports = new Collection();
        foreach (AgentScheduler::get()->agentLabel() as $label) {
            $reports->push($this->lastReport($label));
        }
        return $reports->filter();
    }
    
    public function lastReport(string $label) : ?Report
    {
        $start = time() - 24 * 3600;
        return $this->reports()
                ->where("label", $label)
                ->where("time", ">", $start)
                ->orderByDesc("time")
                ->first();
    }
    
    public function changes()
    {
        return $this->hasMany(StatusChange::class);
    }

    public function lastChanges($count = 10)
    {
        return $this->changes()->orderBy("time", "desc")->limit($count)->get();
    }
    
    public function lastChange() : ?StatusChange
    {
        return $this->changes()->latest("time")->first();
    }
}
