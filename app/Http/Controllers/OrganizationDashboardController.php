<?php

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Http\Request;

/**
 * Public organization dashboard
 */
class OrganizationDashboardController extends Controller
{
    public function dashboard(Organization $organization, string $token)
    {
        if ($organization->dashboard_token != $token) {
            abort(403);
        }

        return view("organization.dashboard", array("organization" => $organization));
    }
}
