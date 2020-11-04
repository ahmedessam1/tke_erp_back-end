<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;


class SupplierAddress extends Model
{
    use SoftDeletes, Sorting, User;
    // FILLABLE
    protected $fillable = [
        'supplier_id', 'address', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    public function contacts() {
        return $this -> hasMany('App\Models\Supplier\SupplierAddressContact');
    }

    // SCOPES
}
