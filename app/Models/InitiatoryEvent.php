<?php

namespace App\Models;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InitiatoryEvent extends Model
{
    use Sorting, User, SoftDeletes;

    // DATES
    protected $dates = ['deleted_at'];

    // FILLABLE
    protected $fillable = [
        'initiatory_type_id', 'model_type', 'model_id', 'description', 'created_by', 'updated_by'
    ];
}
