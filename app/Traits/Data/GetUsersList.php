<?php

namespace App\Traits\Data;

use App\User;

trait GetUsersList {
    public function getUsersListOrderedByName () {
        return User::where('active', 1) -> orderedName() -> pluck('name', 'id');
    }

    public function getUsersListOrderedByID () {
        return User::where('active', 1) -> orderedID() -> pluck('name', 'id');
    }
}