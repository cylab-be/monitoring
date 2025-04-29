<?php

namespace App\Http\Controllers;

use App\Organization;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get the currently selected organization (from session).
     * @return Organization
     */
    protected function organization() : Organization
    {
        return Organization::find(session()->get("organization_id"));
    }
}
