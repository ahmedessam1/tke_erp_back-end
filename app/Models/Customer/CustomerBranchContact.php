<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBranchContact extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'customer_branch_id', 'name', 'phone_number', 'position_id', 'created_by', 'updated_by'
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
