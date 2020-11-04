<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInitiatoryCredit extends Model
{
    use Sorting, User, SoftDeletes;

    // DATES
    protected $dates = ['deleted_at'];

    // FILLABLE
    protected $fillable = [
        'customer_id', 'amount', 'date', 'created_by', 'updated_by'
    ];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function customer () {
        return $this->belongsTo('App\Models\Customer\Customer', 'customer_id')->withTrashed();
    }

    // SCOPES
    public function scopeWithCustomer ($builder) {
        return $builder->with('customer');
    }
}
