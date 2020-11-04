<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductSeasons extends Model
{
    // FILLABLE
    protected $fillable = [
        'product_id', 'season_id'
    ];

    // REMOVING TIMESTAMPS
    public $timestamps = false;
}
