<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPriceList extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'customer_id', 'product_id', 'product_selling_price', 'product_name', 'product_barcode', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product', 'product_id');
    }
}
