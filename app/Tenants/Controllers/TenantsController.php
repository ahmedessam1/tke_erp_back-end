<?php

namespace App\Tenants\Controllers;

use App\Http\Controllers\Controller;
use App\Tenant\Models\Tenant;

class TenantsController extends Controller
{
    public function list()
    {
        return Tenant::select('name', 'domain', 'logo')->get();
    }
}
