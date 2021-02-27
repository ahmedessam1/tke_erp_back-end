<?php

namespace App\Models\Product;

use App\Traits\Eloquent\FieldsPermission;
use App\Traits\Eloquent\Reports\ProductsReportMutator;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use DB;

class Product extends Model
{
    use Searchable, SoftDeletes, Sorting, User, FieldsPermission, ProductsReportMutator;

    // FILLABLE
    protected $fillable = [
        'name', 'code', 'barcode', 'local_code_id', 'category_id', 'description', 'created_by', 'updated_by'
    ];
    // APPENDS
    protected $appends = [
        'deletable',
        'report_total_quantity',
        'report_avg_purchase_price',
        'report_avg_sell_price',
        'report_total_credit',
    ];

    // HIDE DUE TO PERMISSION
    protected $hidden = [];

    // DATES
    protected $dates = ['deleted_at'];

    // SCOUT ALGOLIA SEARCH
    public function searchableAs() {
        // SEARCH INDICE BASED ON LOGGED USER TENANT DOMAIN
        return DB::connection('landlord')->table('tenants')->where('id', Auth::user()->tenant_id)->first()->domain;
    }

    public function toSearchableArray() {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'category_id' => $this->category_id,
        ];
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */

    // HIDING FIELDS THAT USER DOES NOT HAVE PERMISSION TO SEE
    public function toArray()
    {
        if (Auth::check()) {
            $hidden = $this->fieldHidePermission([
                ['roles' => ['super_admin'], 'field' => 'credits'],
                ['roles' => ['super_admin'], 'field' => 'sold'],
            ]);
            $this->hidden = $hidden;
            return parent::toArray();
        }
    }

    public function productLog () {
        return $this->hasOne('App\Models\Product\ProductLog', 'product_id');
    }

    public function localCodes () {
        return $this->belongsTo('App\Models\Product\LocalCode', 'local_code_id');
    }

    public function category () {
        return $this->belongsTo('App\Models\Category\Category');
    }

    public function subcategories () {
        return $this->belongsToMany('App\Models\Category\Subcategory', 'product_subcategories');
    }

    public function images () {
        return $this->hasMany('App\Models\Product\ProductImages')->orderBy('active', 'DESC');
    }

    public function activeImage () {
        return $this->hasMany('App\Models\Product\ProductImages')->where('active', 1);
    }

    public function credits () {
        // GETTING THE PRODUCT CREDIT WHERE IMPORT INVOICE IS ACTIVE AND GETTING THE INITIATORY PRODUCT CREDIT
        // THIS WILL FILTER THE 'ACTIVE' IMPORT INVOICES PRODUCTS ONLY
        return $this->hasMany('App\Models\Product\ProductCredits')
           ->whereNull('import_invoice_id')
           ->orWhereHas('importInvoice', function ($query) {
                $query->where('approve', 1);
            });
    }

    public function sold () {
        return $this->hasMany('App\Models\Product\SoldProducts');
    }

    // MUTATOR

    // SCOPES
    public function scopeWithProductLog ($builder) {
        return $builder->with('productLog');
    }

    public function scopeWithLocalCode ($builder) {
        return $builder->with('localCodes');
    }

    public function scopeWithImages ($builder) {
        return $builder->with('images');
    }

    public function scopeWithCategory ($builder) {
        return $builder->with('category');
    }

    public function scopeWithCategoryAndSubcategories ($builder) {
        return $builder->with('category.subcategories');
    }

    public function scopeWithSubcategories ($builder) {
        return $builder->with('subcategories');
    }

    public function scopeWithCreditsAndWarehouses ($builder) {
        return $builder->with('credits.warehouses');
    }

    public function scopeWithSupplier ($builder) {
        return $builder->with('credits.importInvoice.supplier');
    }

    public function scopeWithSold ($builder) {
        return $builder->with('sold');
    }
}
