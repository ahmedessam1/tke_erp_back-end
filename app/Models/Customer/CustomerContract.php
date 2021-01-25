<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',  'title', 'discount', 'year', 'created_by'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer\Customer');
    }

    // SCOPES
    public function scopeWithCustomer($builder) {
        return $builder->with('customer');
    }
}
