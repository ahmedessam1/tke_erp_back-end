<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerBranchesSellers extends Model
{
    // FILLABLE
    protected $fillable = [
        'seller_id', 'customer_branch_id'
    ];

    public $timestamps = false;
}
