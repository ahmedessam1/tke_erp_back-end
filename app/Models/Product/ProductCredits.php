<?php

namespace App\Models\Product;

use App\Observers\PurchaseProductObserver;
use App\Traits\Eloquent\FieldsPermission;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\Products\ProductsMutator;

class ProductCredits extends Model
{
    use SoftDeletes, Sorting, User, ProductsMutator, FieldsPermission;

    protected $observer = PurchaseProductObserver::class;

    // FILLABLE
    protected $fillable = [
        'product_id',
        'import_invoice_id',
        'quantity',
        'purchase_price',
        'package_size',
        'discount',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    // APPENDS TO COLLECTION
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
        ProductCredits::observe(new PurchaseProductObserver());
    }


    // HIDING FIELDS THAT USER DOES NOT HAVE PERMISSION TO SEE
    public function toArray()
    {
        $hidden = $this -> fieldHidePermission([
            ['roles' => ['super_admin'], 'field' => 'item_net_price'],
            ['roles' => ['super_admin'], 'field' => 'purchase_price'],
            ['roles' => ['super_admin'], 'field' => 'item_net_price_after_tax'],
            ['roles' => ['super_admin'], 'field' => 'credit_net_price'],
            ['roles' => ['super_admin'], 'field' => 'credit_net_price_after_tax'],
            ['roles' => ['super_admin'], 'field' => 'report_avg_purchase_price'],
        ]);
        $this -> hidden = $hidden;

        return parent::toArray();
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function product () {
        return $this -> belongsTo('App\Models\Product\Product') -> with('category') -> withTrashed();
    }

    public function warehouses () {
        return $this -> belongsToMany(
            'App\Models\Warehouse',
            'product_credit_warehouses',
            'product_credit_id',
            'warehouse_id'
        );
    }

    public function importInvoice () {
        return $this -> belongsTo('App\Models\Invoices\ImportInvoice');
    }

    // MUTATOR

    // SCOPES
    public function scopeWithImportInvoice ($builder) {
        return $builder -> with('importInvoice');
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
