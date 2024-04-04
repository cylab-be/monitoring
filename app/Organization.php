<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Organization
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $dashboard_token
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
    
    
    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function url() : string
    {
        return action('OrganizationController@show', ["organization" => $this]);
    }
}
