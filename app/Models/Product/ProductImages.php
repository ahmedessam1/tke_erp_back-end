<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    // FILLABLE
    protected $fillable = [
        'product_id', 'large_image', 'thumbnail_image', 'active'
    ];

    // REMOVING TIMESTAMPS
    public $timestamps = false;
}
