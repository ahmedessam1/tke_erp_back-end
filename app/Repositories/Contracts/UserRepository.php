<?php

namespace App\Repositories\Contracts;

interface UserRepository {
    // RETURN ALL THE ACTIVE USERS ONLY..
    public function getAllActiveUsers();

    // SEARCH FOR USERS
    public function getUsersSearchResult($query);

    // RETURN ALL NOT ACTIVE USERS ONLY..
    public function getAllNotActiveUsers();

    // DEACTIVATE USER
    public function deactivateUser($user);

    // REACTIVATE USER
    public function reactivateUser($user);
}