<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'name', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function branches () {
        return $this -> hasMany('App\Models\Customer\CustomerBranch');
    }

    // SCOPES
    public function scopeGetBranchesDetails (Builder $builder) {
        return $builder -> with('branches.contacts') -> with('branches.sellers');
    }
}
