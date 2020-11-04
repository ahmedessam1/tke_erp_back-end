<?php

namespace App\Repositories;

use App\Events\ActionHappened;
use App\Repositories\Contracts\UserRepository;
use App\User;
use Auth;

class EloquentUserRepository implements UserRepository {
    public function getAllActiveUsers () {
        $users = User::whereNotLoggedUser() -> isActive() -> paginate(30);
        foreach($users as $user)
            $user -> getRoleNames();
        return $users;
    }

    public function getUsersSearchResult ($q) {
        $users = User::where(function($query) use ($q) {
                $query -> where('email', 'LIKE', '%'.$q.'%');
                $query -> orWhere('name', 'LIKE', '%'.$q.'%');
                $query -> orWhereDate('created_at', 'LIKE', '%'.$q.'%');
                // SEARCH FOR ROLES
                $query -> orWhereHas('roles', function($query) use($q) {
                    $query->where('name', 'LIKE', '%'.$q.'%');
                });
            })
            -> isActive()
            -> orderedName()
            -> whereNotLoggedUser()
            -> paginate(30);
        // ASSIGN ROLES
        foreach($users as $user)
            $user -> getRoleNames();
        return $users;

    }

    public function getAllNotActiveUsers () {
        return User::notActive() -> paginate(30);
    }

    public function deactivateUser ($user) {
        $user_id = Auth::user() -> id;
        // DEACTIVATE THE USER
        $user -> update(['active' => 0]);
        // STORE ACTION
        event(new ActionHappened('users deactivate', $user, $user_id));
        return $user;
    }

    public function reactivateUser ($user) {
        $user_id = Auth::user() -> id;
        // DEACTIVATE THE USER
        $user -> update(['active' => 1]);
        // STORE ACTION
        event(new ActionHappened('users reactivate', $user, $user_id));
        return $user;
    }
}