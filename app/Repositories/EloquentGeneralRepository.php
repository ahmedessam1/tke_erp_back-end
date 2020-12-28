<?php

namespace App\Repositories;

use App\Repositories\Contracts\GeneralRepository;
use Auth;

class EloquentGeneralRepository implements GeneralRepository
{
    private function getAuthUserId()
    {
        return Auth::user()->id;
    }


}
