<?php

namespace App\Traits\Data;

use App\Models\Customer\Customer;
use App\Models\Customer\CustomerBranch;
use App\Models\Customer\EloquentReportProductRepository;
use Auth;

trait GetCustomersList {
    // CUSTOMERS
    public function getCustomersListOrderedByName () {
        return Customer::orderedName() -> pluck('name', 'id');
    }

    public function getCustomersListPerUserOrderedByName () {
        $user_id = Auth::user() -> id;
        return Customer::orderedName() -> whereHas('branches', function ($q) use ($user_id) {
                $q->whereHas('sellers', function ($query) use ($user_id) {
                    $query->where('seller_id', $user_id);
                });
            })
            -> get()
            -> pluck('customer_and_branch', 'id');
    }

    public function getCustomersListOrderedByID () {
        return Customer::orderedId() -> orderBypluck('name', 'id');
    }

    // CUSTOMERS BRANCHES
    public function getCustomersBranchesListOrderedByName () {
        return CustomerBranch::getCustomer() -> get() -> pluck('customer_and_branch', 'id');
    }

    public function getCustomersBranchesListPerUserOrderedByName () {
        $user_id = Auth::user() -> id;
        return CustomerBranch::getCustomer()
            -> whereHas('sellers', function($query) use ($user_id) {
                $query -> where('seller_id', $user_id);
            })
            -> get()
            -> pluck('customer_and_branch', 'id');
    }

    public function getCustomersBranchesListOrderedByID () {
        return CustomerBranch::getCustomer() -> get() -> pluck('customer_and_branch', 'id');
    }
}