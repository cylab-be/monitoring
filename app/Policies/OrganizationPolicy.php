<?php

namespace App\Policies;

use App\User;
use App\Organization;

use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return true;
    }
    
    public function show(User $user, Organization $organization)
    {
        return $user->ownsOrganization($organization);
    }
    
    public function create(User $user)
    {
        return true;
    }
    
    public function update(User $user, Organization $organization)
    {
        return $user->ownsOrganization($organization);
    }
    
    public function destroy(User $user, Organization $organization)
    {
        return $user->ownsOrganization($organization);
    }
}
