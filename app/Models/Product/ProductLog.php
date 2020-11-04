<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    protected $fillable = ['product_id', 'available_quantity', 'average_purchase_price', 'average_sell_price'];

    // RELATIONSHIPS
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product () {
        return $this->belongsTo(Product::class);
    }
}
