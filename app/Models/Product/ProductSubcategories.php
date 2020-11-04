<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategories extends Model
{
    // FILLABLE
    protected $fillable = [
        'product_id', 'subcategory_id'
    ];

    // REMOVING TIMESTAMPS
    public $timestamps = false;
}
