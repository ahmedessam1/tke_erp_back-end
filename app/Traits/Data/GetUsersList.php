<?php

namespace App\Traits\Data;

use App\User;
use Auth;

trait GetUsersList
{
    public function getUsersListOrderedByName()
    {
        $tenant_id = Auth::user()->tenant_id;
        return User::where('active', 1)->where('tenant_id', $tenant_id)->orderedName()->pluck('name', 'id');
    }

    public function getUsersListOrderedByID()
    {
        $tenant_id = Auth::user()->tenant_id;
        return User::where('active', 1)->where('tenant_id', $tenant_id)->orderedID()->pluck('name', 'id');
    }

    public function getLoggedUserName()
    {
        $tenant_id = Auth::user()->tenant_id;
        return User::where('active', 1)->where('tenant_id', $tenant_id)->where('id', Auth::user()->id)->pluck('name', 'id');
    }
}
