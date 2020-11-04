<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class LocalCode extends Model
{
    // FILLABLE
    protected $fillable = [
        'local_code'
    ];

    // REMOVING TIMESTAMPS
    public $timestamps = false;
}
