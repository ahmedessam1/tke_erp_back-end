<?php

namespace App\Tenant\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'landlord';
}
