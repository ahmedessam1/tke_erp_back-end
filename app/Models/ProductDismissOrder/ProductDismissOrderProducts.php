<?php

namespace App\Models\ProductDismissOrder;

use App\Observers\DismissalProductObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductDismissOrderProducts extends Model
{
    use SoftDeletes;

    // FILLABLE
    protected $fillable = [
        'product_dismiss_order_id', 'product_id', 'reason_id', 'quantity'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // REGISTER OBSERVER
    public static function boot() {
        parent::boot();
        ProductDismissOrderProducts::observe(new DismissalProductObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function productDismissOrder () {
        return $this -> belongsTo('App\Models\ProductDismissOrder\ProductDismissOrder', 'product_dismiss_order_id');
    }

    public function productDismissReason () {
        return $this -> belongsTo('App\Models\ProductDismissReasons', 'reason_id');
    }

    public function product () {
        return $this -> belongsTo('App\Models\Product\Product') -> with('category') -> withTrashed();
    }

    // MUTATORS

    // SCOPES
    public function scopeWithProductDismissOrder ($builder) {
        return $builder -> with('productDismissOrder');
    }

    public function scopeWithProductDismissReason ($builder) {
        return $builder -> with('productDismissReason');
    }

    public function scopeWithProduct ($builder) {
        return $builder -> with('product');
    }
}
