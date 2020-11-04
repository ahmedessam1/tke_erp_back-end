<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SupplierAddressContact extends Model
{
    use SoftDeletes;
    // FILLABLE
    protected $fillable = [
        'supplier_address_id', 'name', 'phone_number', 'position_id', 'created_by', 'updated_by'
    ];

    // DATES

    // RELATIONSHIPS
    public function position() {
        return $this -> belongsTo('App\Models\Position', 'position_id');
    }

    // SCOPES
}
