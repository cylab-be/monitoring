<?php

namespace App\Policies;

use App\User;
use App\Server;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }

    public function save(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }

    public function destroy(User $user, Server $server)
    {
        return $user->ownsOrganization($server->organization);
    }
}
