<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function organizations()
    {
        return $this->belongsToMany('App\Organization');
    }

    public function ownsOrganization(Organization $organization) {
        foreach ($this->organizations as $o) {
            if ($o->id == $organization->id) {
                return true;
            }
        }

        return false;
    }

    public static function findByEmail($email) {
        return self::where("email", $email)->first();
    }

}
