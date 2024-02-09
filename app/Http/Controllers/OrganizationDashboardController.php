<?php

namespace App\Http\Controllers;

use App\Organization;

/**
 * Public organization dashboard
 */
class OrganizationDashboardController extends Controller
{

    public function json(Organization $organization, string $token)
    {
        if ($organization->dashboard_token != $token) {
            abort(403);
        }

        return response()->json($organization);
    }
}
