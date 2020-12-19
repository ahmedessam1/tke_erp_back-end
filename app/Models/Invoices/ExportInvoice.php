<?php

namespace App\Models\Invoices;

use App\Observers\Invoice\ExportInvoiceObserver;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\Status;
use App\Traits\Eloquent\User;
use App\Traits\Logic\InvoiceCalculations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportInvoice extends Model
{
    use SoftDeletes, InvoiceCalculations, Sorting, User, Status;

    protected $casts = ['approve' => 'integer'];
    // FILLABLE
    protected $fillable = [
        'name', 'seller_id', 'number', 'customer_branch_id', 'date', 'tax', 'discount', 'created_by', 'updated_by'
    ];

    // APPENDS
    protected $appends = [
        'title',
        'total_after_discount',
        'total_after_tax'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // REGISTER OBSERVER
    public static function boot()
    {
        parent::boot();
        ExportInvoice::observe(new ExportInvoiceObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function customerBranch()
    {
        return $this->belongsTo('App\Models\Customer\CustomerBranch');
    }

    public function seller()
    {
        return $this->belongsTo('App\User', 'seller_id');
    }

    public function soldProducts()
    {
        return $this->hasMany('App\Models\Product\SoldProducts');
    }

    // MUTATORS
    public function getTitleAttribute()
    {
        $title = '#' . $this->number . ' - ' . $this->name . ' - (' . Carbon::parse($this->date)->format('d-m-Y') . ')';
        if ($this->customerBranch)
            $title .= ' - ' . $this->customerBranch->customer->name . '  (' . $this->customerBranch->address . ')';
        return $title;
    }

    public function getTotalAfterDiscountAttribute()
    {
        return $this->calculateTotalAfterDiscount($this->net_total, $this->discount);
    }

    public function getTotalAfterTaxAttribute()
    {
        return $this->calculateTotalAfterTax($this->net_total, $this->tax, $this->discount);
    }

    // SCOPES
    public function scopeWithCustomerBranch($builder)
    {
        return $builder->with('customerBranch.customer')
            ->with('customerBranch.sellers')
            ->with('customerBranch.contacts');
    }

    public function scopeWithSeller($builder)
    {
        return $builder->with('seller');
    }

    public function scopeWithSoldProductsImages($builder)
    {
        // REMOVED with('soldProducts.product.credits') for optimizing the response
        return $builder->with('soldProducts.product.images');
    }
}
