<?php

namespace App\Models\Invoices;

use App\Observers\Invoice\ImportInvoiceObserver;
use App\Traits\Eloquent\Status;
use App\Traits\Logic\InvoiceCalculations;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;


class ImportInvoice extends Model
{
    use SoftDeletes, InvoiceCalculations, Sorting, User, Status;

    // FILLABLE
    protected $fillable = [
        'name', 'number', 'supplier_id', 'date', 'tax', 'discount', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // APPENDS
    protected $appends = ['title', 'total_after_discount', 'total_after_tax'];

    // MUTATORS
    public function getTitleAttribute()
    {
        return '#' . $this->number . ' - ' . $this->name . ' - (' . $this->date  . ')'
        . ' - ' . $this->supplier->name;
    }

    public function getTotalAfterDiscountAttribute () {
        return $this->calculateTotalAfterDiscount($this->net_total, $this->discount);
    }

    public function getTotalAfterTaxAttribute () {
        return $this->calculateTotalAfterTax($this->net_total, $this->tax, $this->discount);
    }

    // REGISTER OBSERVER
    public static function boot() {
        parent::boot();
        ImportInvoice::observe(new ImportInvoiceObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function supplier () {
        return $this -> belongsTo('App\Models\Supplier\Supplier');
    }

    public function productCredits () {
        return $this -> hasMany('App\Models\Product\ProductCredits', 'import_invoice_id');
    }

    // SCOPES
    public function scopeWithSupplier ($builder) {
        return $builder -> with('supplier.addresses.contacts');
    }

    public function scopeWithProductCredits ($builder) {
        return $builder -> with('productCredits.product.images') -> with('productCredits.warehouses');
    }
}
