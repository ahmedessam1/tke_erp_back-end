<?php

namespace App\Models\ProductDismissOrder;
use App\Observers\Invoice\DismissInvoiceObserver;
use App\Traits\Eloquent\Status;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;

class ProductDismissOrder extends Model
{
    use SoftDeletes, Sorting, Status, User;

    // FILLABLE
    protected $fillable = [
        'title', 'approve', 'notes', 'created_by', 'updated_by'
    ];

    // REGISTER OBSERVER
    public static function boot() {
        parent::boot();
        ProductDismissOrder::observe(new DismissInvoiceObserver());
    }

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function productDismissOrderProducts () {
        return $this -> hasMany(
            'App\Models\ProductDismissOrder\ProductDismissOrderProducts',
            'product_dismiss_order_id'
        );
    }


    // SCOPES
    public function scopeWithProductDismissOrderProducts ($builder) {
        return $builder -> with('productDismissOrderProducts.productDismissReason')
            -> with('productDismissOrderProducts.product.images');
    }
}
