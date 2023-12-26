<?php

namespace App\Policies;

use App\User;
use App\Server;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;


    public function create(User $user)
    {
        return true;
    }
    
    public function show(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }
    
    public function update(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }
    
    public function destroy(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }
}
