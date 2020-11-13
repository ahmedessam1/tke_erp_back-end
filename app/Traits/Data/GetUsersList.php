<?php

namespace App\Traits\Data;

use App\User;
use Auth;

trait GetUsersList
{
    public function getUsersListOrderedByName()
    {
        return User::where('active', 1)->orderedName()->pluck('name', 'id');
    }

    public function getUsersListOrderedByID()
    {
        return User::where('active', 1)->orderedID()->pluck('name', 'id');
    }

    public function getLoggedUserName()
    {
        return User::where('active', 1)->where('id', Auth::user()->id)->pluck('name', 'id');
    }
}
