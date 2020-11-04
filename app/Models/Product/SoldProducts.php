<?php

namespace App\Models\Product;

use App\Observers\SoldProductObserver;
use App\Traits\Eloquent\Reports\SoldProductsMutator;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoldProducts extends Model
{
    use SoftDeletes, Sorting, User, SoldProductsMutator;

    // FILLABLE
    protected $fillable = [
        'export_invoice_id',
        'product_id',
        'quantity',
        'sold_price',
        'discount',
        'created_by',
        'updated_by'
    ];

    // APPENDS
    protected $appends = [
        'item_net_price',
        'item_net_price_after_tax',
        'credit_net_price',
        'credit_net_price_after_tax',
        'item_net_price_without_invoice_tax_and_discount',
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // REGISTER OBSERVER
    public static function boot() {
        parent::boot();
        SoldProducts::observe(new SoldProductObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function product () {
        return $this -> belongsTo('App\Models\Product\Product') -> with('category') -> withTrashed();
    }

    public function exportInvoice () {
        return $this -> belongsTo('App\Models\Invoices\ExportInvoice');
    }

    // MUTATOR


    // SCOPES
    public function scopeWithExportInvoice ($builder) {
        return $builder -> with('exportInvoice');
    }

    public function scopeWithProductAndImages ($builder) {
        return $builder -> with('product.images');
    }

    public function scopeWithWarehouses ($builder) {
        return $builder -> with('warehouses');
    }

    public function scopeWithProduct ($builder) {
        return $builder -> with('product');
    }
}
