<?php

namespace App;

use League\CommonMark\CommonMarkConverter;

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
 * @property string $description
 * @property int $size rack size, expressed in "U"
 * @property int $position position in rack, expressed in "U", starting from the bottom
 * @property string $token
 * @property string $read_token
 * @property ?\App\ServerInfo $info
 * @property \App\Organization $organization
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

    // define attributes that can be appended when serializing to json
    // used in OrganizationDashboardController@json
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

        foreach ($this->getSensorsNOK() as $report) {
            $failing_sensors[] = $report->title;
        }

        return $failing_sensors;
    }

    public function getLastRecordTimeAttribute() : int
    {
        return $this->lastSummary()->time;
    }

    // -------------------------------------

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

    public function info()
    {
        return $this->hasOne(ServerInfo::class);
    }

    /**
     *
     * @return \App\Status
     */
    public function status() : Status
    {
        return $this->lastSummary()->status();
    }

    /**
     *
     * @return Collection<Report>
     */
    public function getSensorsNOK() : Collection
    {
        $summary = $this->lastSummary();

        if ($summary->status_code == 0) {
            return new Collection();
        }

        return $summary->reports()->filter(
            function (Report $report) {
                return $report->status_code > 0;
            }
        );
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }


    public function summaries()
    {
        return $this->hasMany(ReportSummary::class);
    }

    private $last_summary = null;

    public function lastSummary() : ReportSummary
    {
        if (! is_null($this->last_summary)) {
            return $this->last_summary;
        }

        // try to fetch from DB
        $summary = $this->summaries()->orderByDesc("id")->first();
        if (! is_null($summary)) {
            $this->last_summary = $summary;
            return $this->last_summary;
        }

        $this->last_summary = ReportSummary::default($this);
        return $this->last_summary;
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

    public function descriptionAsHTML() : string
    {
        $converter = new CommonMarkConverter();
        return $converter->convertToHtml($this->description ?? '');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function url() : string
    {
        return route("servers.show", ["server" => $this]);
    }

    public function toCytoscape() : array
    {
        return ["data" => [
            "id" => $this->cytoId(),
            "label" => $this->name,
            "type" => "device",
            "url" => $this->url()]];
    }

    /**
     * Get a unique ID usable in Cytoscape.
     * @return string
     */
    public function cytoId() : string
    {
        return "#server-" . $this->id;
    }
}
