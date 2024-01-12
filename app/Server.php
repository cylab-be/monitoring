<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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

    /**
     * Last record from this server (used for caching).
     * @var String
     */
    private $last_record = null;
    private $records_1day = null;
    private $info = null;

    private static $sensors = [
        \App\Sensor\LoadAvg::class,
        \App\Sensor\MemInfo::class,
        \App\Sensor\Ifconfig::class,
        \App\Sensor\Netstat::class,
        \App\Sensor\ListeningPorts::class,
        \App\Sensor\Reboot::class,
        \App\Sensor\Updates::class,
        \App\Sensor\Disks::class,
        \App\Sensor\Inodes::class,
        \App\Sensor\Ssacli::class,
        \App\Sensor\Perccli::class,
        \App\Sensor\Date::class,
        \App\Sensor\ClientVersion::class,
        \App\Sensor\Heartbeat::class,
        \App\Sensor\CPUtemperature::class,
        \App\Sensor\USBtemperature::class
    ];

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

    public function lastRecord() : ?Record
    {
        if ($this->last_record == null) {
            $this->last_record = $this->records()->orderBy("time", "desc")->first();
        }

        return $this->last_record;
    }

    /**
     * Get the last day of data.
     */
    public function lastRecords1Day() : Collection
    {
        if ($this->records_1day == null) {
            $start = time() - 24 * 3600;
            $this->records_1day = $this->records()
                    ->where("time", ">", $start)
                    ->orderBy("time")
                    ->get();
        }

        return $this->records_1day;
    }

    public function hasData() : bool
    {
        return $this->lastRecord() != null;
    }

    public function info() : ServerInfo
    {
        if (is_null($this->info)) {
            $this->info = new ServerInfo($this->lastRecord());
        }

        return $this->info;
    }

    /**
     *
     * @return \App\Status
     */
    public function status() : Status
    {
        return Status::max($this->reports());
    }

    public function getSensorsNOK()
    {
        $sensorsNOK = [];
        foreach ($this->reports() as $sensor) {
            if ($sensor->status()->code() > 0) {
                $sensorsNOK[] = $sensor;
            }
        }
        return $sensorsNOK;
    }
    
    private $reports = null;

    public function reports()
    {
        if (is_null($this->reports)) {
            $records = $this->lastRecords1Day();
            $serverinfo = $this->info();

            $this->reports = [];
            foreach (self::$sensors as $sensor) {
                try {
                     $report = (new $sensor)->analyze($records, $serverinfo);
                     $this->reports[] = $report;
                } catch (\Throwable $ex) {
                    Log::error('Sensor failed : ' . $ex->getTraceAsString());
                    $report = new Report($sensor, Status::unknown());
                    $report->setHTML("<p>Agent crashed...</p>");
                    $this->reports[] = $report;
                }
            }
        }
        
        return $this->reports;
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
