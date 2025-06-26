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
    // don't show dashboard token when serializing to json
    protected $hidden = ['dashboard_token'];

    // add servers when serializing to json
    // https://laravel.com/docs/8.x/eloquent-serialization#appending-values-to-json
    protected $appends = ['servers'];

    protected function getServersAttribute()
    {
        return $this->servers()->orderBy("name")->get();
    }


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
            return $device1->lastSummary()->status_code < $device2->lastSummary()->status_code;
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

    public function toAnsibleInventory() : string
    {
        return $this->subnets->map(fn($subnet) => $subnet->toAnsibleInventory())
                ->union($this->tags->map(fn($tag) => $tag->toAnsibleInventory()))
                ->implode("\n");
    }
}
