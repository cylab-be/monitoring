<?php

namespace App;

use League\CommonMark\CommonMarkConverter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
 * @property DatabaseCollection|array $ips
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
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
    
    // ------------------------------------- used by API calls
    
    public function toInventory() : array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "token" => $this->token,
            "addresses" => $this->addresses(),
            "tags" => $this->tags->map(fn(Tag $tag) => $tag->name),
        ];
    }
    
    public function toDashboard() : object
    {
        return (object) [
            "name" => $this->name,
            "url" => $this->url(),
            "status" => $this->status()->jsonSerialize(),
            "last_record_time" => $this->lastSummary()->time,
            // only keep the title of failing sensors
            "failing_sensors" => $this->getSensorsNOK()->map(fn(Report $report) => $report->title)->values()->toArray(),
            "tags" => $this->tags->map(fn(Tag $tag) => $tag->name)->toArray(),
        ];
    }
    
    // ------------------------------------- PROPERTIES
    
    protected $casts = ['properties' => 'array'];
    
    private $properties_handler = null;
    
    public function properties() : ArrayField
    {
        if ($this->properties_handler == null) {
            $this->properties_handler = new ArrayField($this, "properties");
        }
        return $this->properties_handler;
    }
    
    public function icon() : Icon
    {
        return new Icon($this->properties()->get("icon"));
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
     *
     * @param string $label
     * @return DatabaseCollection<Record>
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

        // https://stackoverflow.com/questions/76729231/larastan-complains-about-collection-methods-paramaters-after-upgrading-to-larave
        // @phpstan-ignore-next-line
        return $summary->reports()->filter(fn(Report $report) => $report->status_code > 0);
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

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function url() : string
    {
        return route("servers.show", ["server" => $this]);
    }
    
    public function customInstallationInstructions() : string
    {
        return Str::of($this->organization->properties()->getOrDefault("instructions", ""))
                ->replace('%i%', (string) $this->id)
                ->replace('%n%', $this->name)
                ->replace('%t%', $this->token)
                ->__toString();
    }
    
    // ------------------------------ CYTOSCAPE

    public function toCytoscape() : array
    {
        return [
            "data" => [
                "id" => $this->cytoId(),
                "label" => $this->name,
                "type" => "device",
                "url" => $this->url()],
            "style" => [
                "background-image" => $this->icon()->url(),
                'background-opacity' => 0,           // Hides the node's solid background
                'border-width' => 0,
            ]];
    }

    /**
     * Get a unique ID usable in Cytoscape.
     * @return string
     */
    public function cytoId() : string
    {
        return "#server-" . $this->id;
    }
    
    // -------------------------- IP addresses & subnets
    
    /**
     * Manual IP addresses
     */
    public function ips()
    {
        return $this->hasMany(Ip::class);
    }

    /**
     * Full list of IP addresses : manual IPs + autodetected
     * @return array<string>
     */
    public function addresses() : array
    {
        return $this->info->addresses();
    }
    
    public function subnets() : Collection
    {
        
        $subnets = new Collection();
        foreach ($this->organization->subnets as $subnet) {
            foreach ($this->addresses() as $ip) {
                if ($subnet->hasAddress($ip)) {
                    $subnets->add($subnet);
                    
                    // go to next subnet
                    break;
                }
            }
        }
        
        return $subnets;
    }
}
