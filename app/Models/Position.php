<?php

namespace App\Models;

use App\Traits\Eloquent\Sorting;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use Sorting;
    // FILLABLE
    protected $fillable = ['name'];
}
