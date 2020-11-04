<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBranch extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'customer_id', 'address', 'discount', 'notes', 'created_by', 'updated_by'
    ];

    // APPENDS
    protected $appends = ['customer_and_branch'];

    // DATES
    protected $dates = ['deleted_at'];

    // MUTATORS
    public function getCustomerAndBranchAttribute() {
        return $this -> customer -> name . ' - ' . $this -> address;
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function customer () {
        return $this -> belongsTo('App\Models\Customer\Customer', 'customer_id');
    }

    public function contacts () {
        return $this -> hasMany('App\Models\Customer\CustomerBranchContact');
    }

    public function sellers () {
        return $this -> belongsToMany('App\User', 'customer_branches_sellers', 'customer_branch_id', 'seller_id');
    }

    public function invoices () {
        return $this -> hasMany('App\Models\Invoices\ExportInvoice', 'customer_branch_id') -> approved();
    }

    // SCOPES
    public function scopeGetCustomer (Builder $builder) {
        return $builder -> with('customer');
    }

    public function scopeWithSellersAndContacts (Builder $builder) {
        return $builder -> with('sellers') -> with('contacts');
    }

    public function scopeWithInvoices (Builder $builder) {
        return $builder -> with('invoices');
    }
}
