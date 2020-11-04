<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;

class Season extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'name', 'starting_date', 'ending_date', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */

    // SCOPES

}
