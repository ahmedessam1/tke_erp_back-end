<?php

namespace App\Models\Refund;

use App\Observers\Invoice\RefundInvoiceObserver;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\Status;
use App\Traits\Eloquent\User;
use App\Traits\Logic\InvoiceCalculations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use InvoiceCalculations, SoftDeletes, Sorting, Status, User;

    // FILLABLE
    protected $fillable = [
        'title', 'assigned_user_id', 'number', 'model_id', 'tax', 'discount', 'approve', 'notes', 'date', 'type', 'created_by', 'updated_by'
    ];

    // APPENDS
    protected $appends = [
        'refund_title',
        'total_after_discount',
        'total_after_tax'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // REGISTER OBSERVER
    public static function boot() {
        parent::boot();
        Refund::observe(new RefundInvoiceObserver());
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function customerBranch () {
        return $this -> belongsTo('App\Models\Customer\CustomerBranch', 'model_id');
    }

    public function supplier () {
        return $this -> belongsTo('App\Models\Supplier\Supplier', 'model_id');
    }

    public function refundedProducts () {
        return $this -> hasMany('App\Models\Refund\RefundProduct', 'refund_id');
    }

    public function assignedUser ()
    {
        return $this->belongsTo('App\User', 'assigned_user_id');
    }

    // MUTATORS
    public function getRefundTitleAttribute()
    {
        $refund_to = '';
        if ($this->type === 'in')
            $refund_to = $this->customerBranch->customer->name . ' (' . $this->customerBranch->address . ')';
        else
            $refund_to = $this->supplier->name;

        return '#' . $this->number . ' - ' . $this->title . ' - (' . $this->date . ')'
            . ' - ' . $refund_to;
    }

    public function getTotalAfterDiscountAttribute () {
        return $this->calculateTotalAfterDiscount($this->net_total, $this->discount);
    }

    public function getTotalAfterTaxAttribute () {
        return $this->calculateTotalAfterTax($this->net_total, $this->tax, $this->discount);
    }


    // SCOPES
    public function scopeWithCustomerBranch ($builder) {
        return $builder -> with('customerBranch');
    }

    public function scopeWithSupplier ($builder) {
        return $builder -> with('supplier');
    }

    public function scopeWithAssignedUser ($builder) {
        return $builder -> with('assignedUser');
    }

    public function scopeWithCustomer ($builder) {
        return $builder -> with('customerBranch.customer');
    }

    public function scopeWithCustomerSellers ($builder) {
        return $builder -> with('customerBranch.sellers');
    }

    public function scopeWithRefundedProducts ($builder) {
        return $builder -> with('refundedProducts');
    }
}
