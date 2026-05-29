<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Organization
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $dashboard_token
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Rack[] $racks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Subnet[] $subnets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Server[] $servers
 * @property-read int|null $servers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereDashboardToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Organization extends Model
{
    
    // --------------------------- PROPERTIES
    protected $casts = ['properties' => 'array'];
    
    private $properties_handler = null;
    
    public function properties() : ArrayField
    {
        if ($this->properties_handler == null) {
            $this->properties_handler = new ArrayField($this, "properties");
        }
        return $this->properties_handler;
    }
    
    // -------------------------
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // must manually set a default value for the properties field
        // to avoid SQLSTATE[HY000]: General error: 1364 Field 'properties' doesn't have a default value
        $this->properties = [];
    }

    // ------------------------- RELATIONS

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function devicesByStatus() : Collection
    {
        return $this->servers->sort(function (Server $device1, Server $device2) {
            return $device1->lastSummary()->status_code < $device2->lastSummary()->status_code ? 1 : -1;
        });
    }


    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function subnets()
    {
        return $this->hasMany(Subnet::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
    
    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function url() : string
    {
        return action('OrganizationController@show', ["organization" => $this]);
    }

    public function toCytoscape() : array
    {
        $r = [];
        foreach ($this->servers as $server) {
            $r[] = $server->toCytoscape();
        }

        foreach ($this->subnets as $subnet) {
            $r = array_merge($r, $subnet->toCytoscape());
        }
        return $r;
    }
    
    // ------------------------ API calls

    public function inventory() : array
    {
        return $this->servers->map(fn(Server $server) => $server->toInventory())->toArray();
    }
    
    public function dashboard() : object
    {
        return (object) [
            "name" => $this->name,
            "devices" => $this->servers
                ->sort(fn(Server $s1, Server $s2) => ($s1->status()->code() < $s2->status()->code()) ? 1 : -1)
                ->map(fn(Server $server) => $server->toDashboard())
                ->values()
                ->toArray(),
        ];
    }
}
