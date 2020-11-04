<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Models\Customer\CustomerPayment;
use App\Repositories\Contracts\CustomerRepository;
use App\Traits\Logic\CustomerCreditCalculations;
use App\Traits\Logic\InvoiceCalculations;
use App\Models\Customer\CustomerBranch;
use App\Models\Invoices\ExportInvoice;
use App\Events\TransactionHappened;
use App\Models\Customer\Customer;
use App\Events\ActionHappened;
use Auth;
use DB;

class EloquentCustomerRepository implements CustomerRepository {
    use CustomerCreditCalculations, InvoiceCalculations;
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function getAllActiveCustomers ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        $redis_key = 'customers:'.$request['page'].':sort_by:'.$request['sort_by'].':sort_type:'.$request['sort_type'];

        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $customers = $this->cache->remember($redis_key, function () use ($sorting) {
            return json_encode(Customer::withCreatedByAndUpdatedBy()
                ->getBranchesDetails()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30));
        }, config('constants.cache_expiry_minutes_small'));
        return json_decode($customers);
    }

    public function searchCustomers ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return Customer::withCreatedByAndUpdatedBy() -> getBranchesDetails() -> orderedName()
            -> where('name', 'LIKE', '%'.$q.'%')
            -> orWhereHas('branches', function ($query) use ($q) {
                $query -> where('address', 'LIKE', '%'.$q.'%');
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            -> paginate(30);
    }

    public function showCustomer ($customer_id) {
        return Customer::GetBranchesDetails() -> find($customer_id);
    }

    public function showCustomerBranch ($customer_branch_id) {
        return CustomerBranch::withCreatedByAndUpdatedBy()
            -> getCustomer()
            -> withSellersAndContacts()
            -> find($customer_branch_id);
    }

    public function addCustomer ($request) {
        // DELETE CACHED CUSTOMERS
        $this->cache->forgetByPattern('customers:*');
        return DB::transaction(function () use ($request) {
            // STORE CUSTOMER
            $new_customer = Customer::create([
                'name'          => $request -> name,
                'created_by'    => $this->getAuthUserId(),
            ]);

            // STORE BRANCHES, BRANCH CONTACTS, SELLERS
            $this -> storeBranches($new_customer -> id, $request -> branches);

            // FIRE ACTION
            event(new ActionHappened('customer added', $request, $this -> getAuthUserId()));
            return $new_customer;
        });
    }

    public function editCustomer ($customer_id) {
        return Customer::getBranchesDetails() -> find($customer_id);
    }

    public function updateCustomer ($request, $customer_id) {
        // DELETE CACHED CUSTOMERS
        $this->cache->forgetByPattern('customers:*');
        return DB::transaction(function () use ($request, $customer_id) {
            // GET CUSTOMER
            $customer = Customer::find($customer_id);

            // UPDATE CUSTOMER BASIC INFO\
            $new_customer_info = [
                'name'          => $request -> name,
                'updated_by'    => $this->getAuthUserId(),
            ];
            $customer -> update($new_customer_info);

            // FIRE ACTION
            event(new ActionHappened('customer edited: '.$customer -> id, $request, $this -> getAuthUserId()));
            return $customer;
        });
    }

    public function addBranch ($request)  {
        // DELETE CACHED CUSTOMERS
        $this->cache->forgetByPattern('customers:*');
        return DB::transaction(function () use ($request) {
            $customer_id = $request -> customer_id;

            // STORE BRANCHES, BRANCH CONTACTS, SELLERS
            $this -> storeBranches($customer_id, $request -> branches);

            // FIRE ACTION
            event(new ActionHappened('customer branch added to: '.$customer_id, $customer_id, $this -> getAuthUserId()));
            return Customer::getBranchesDetails() -> find($customer_id) -> branches;
        });
    }

    public function deleteCustomerBranch($customer_branch_id) {
        // DELETE CACHED CUSTOMERS
        $this->cache->forgetByPattern('customers:*');
        $customer_branch = CustomerBranch::find($customer_branch_id);
        $customer = $customer_branch -> customer;
        $customer_branch -> delete();
        event(new ActionHappened('customer branch deleted', $customer_branch, $this -> getAuthUserId()));
        return $customer;
    }

    public function deleteCustomer($customer_id) {
        // DELETE CACHED CUSTOMERS
        $this->cache->forgetByPattern('customers:*');
        $customer = Customer::find($customer_id);
        $customer -> delete();
        event(new ActionHappened('customer deleted', $customer, $this -> getAuthUserId()));
        return $customer;
    }

    public function sellers($customer_branch_id) {
        return CustomerBranch::find($customer_branch_id) -> sellers() -> pluck('name', 'id');
    }
    /* *********************************************
     * ************ CUSTOMERS INVOICES *************
     * *********************************************/
    // INVOICES
    public function invoices ($customer_branch_id) {
        return ExportInvoice::approved()
            -> withSeller()
            -> withCustomerBranch()
            -> where('customer_branch_id', $customer_branch_id)
            -> paginate(30);
    }

    // INVOICES LIST
    public function invoicesList ($customer_branch_id) {
        return ExportInvoice::approved()
            -> where('customer_branch_id', $customer_branch_id)
            -> get()
            -> pluck('title', 'id');
    }

    // INVOICES SEARCH
    public function invoicesSearch ($q, $customer_branch_id) {
        $invoices = ExportInvoice::approved()
            -> withSeller()
            -> withCustomerBranch()
            -> withCreatedByAndUpdatedBy()
            -> orderedName()
            -> where('customer_branch_id', $customer_branch_id)
            -> where(function ($query) use ($q) {
                $query -> where('name', 'LIKE', '%'.$q.'%');
                $query -> orWhere('number', 'LIKE', '%'.$q.'%');
                $query -> orWhere('date', 'LIKE', '%'.$q.'%');
                // SEARCH BY CUSTOMER NAME
                $query -> orWhereHas('customerBranch.customer', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%'.$q.'%');
                });
                // SEARCH BY CUSTOMER BRANCH NAME
                $query -> orWhereHas('customerBranch', function ($query) use ($q) {
                    $query -> where('address', 'LIKE', '%'.$q.'%');
                });
            })
            -> paginate(30);
        return $invoices;
    }

    // CREDIT
    public function credit ($customer_id) {
        // CALCULATE THE INVOICES TOTAL
        $invoices_total = $this -> customerInvoicesTotal($customer_id);

        // CALCULATE THE CUSTOMER PAYMENTS TOTAL
        $payments_total = $this -> customerPaymentsTotal($customer_id);

        // CALCULATE THE CUSTOMER INITIATORY CREDIT
        $initiatory_credit_total = $this -> customerInitiatoryCreditTotal($customer_id);

        // CALCULATE REFUNDS
        $refunds_total = $this -> customerRefundsTotal($customer_id);

        // CALCULATE NET CREDIT
        $net_credit = $this -> netCredit();

        return [
            'invoices_total'    => $invoices_total,
            'payments_total'    => $payments_total,
            'initiatory_credit_total' => $initiatory_credit_total,
            'refunds_total' => $refunds_total,
            'net_credit'        => $net_credit,
        ];
    }


    /* *********************************************
     * ************* CUSTOMER PAYMENTS *************
     * *********************************************/
    public function payments () {
        return CustomerPayment::with('paymentType')
            -> withCustomer()
            -> orderBy('approve', 'ASC')
            -> paginate(30);
    }

    public function paymentsSearch ($q) {
        return CustomerPayment::with('paymentType')
            -> withCustomer()
            -> where('amount', 'LIKE', '%'.$q.'%')
            -> orWhereHas('customer', function ($query) use ($q) {
                $query -> where('name', 'LIKE', '%'.$q.'%');
            })
            -> orWhereHas('paymentType', function ($query) use ($q) {
                $query -> where('type', 'LIKE', '%'.$q.'%');
            })
            -> orWhere('notes', 'LIKE', '%'.$q.'%')
            -> orWhereDate('created_at', 'LIKE', '%'.$q.'%')
            -> paginate(30);
    }

    public function paymentsAdd ($request) {
        return DB::transaction(function () use ($request) {
            // ADDING THE CUSTOMER BRANCH PAYMENT
            $payment_fillable_values = array_merge(
                $request -> all(),
                ['created_by' => $this->getAuthUserId()]
            );

            $new_customer_payment = CustomerPayment::create($payment_fillable_values);

            // STORE ACTION
            event(new ActionHappened('customer payment add: '.$request -> amount, $request, $this -> getAuthUserId()));
            return $new_customer_payment ;
        });
    }

    public function paymentsApprove ($payment_id) {
        return DB::transaction(function () use ($payment_id) {
            $payment = CustomerPayment::find($payment_id);
            $payment -> approve = 1;
            $payment -> save();
            // TRANSACTION EVENT STORE
            event(new TransactionHappened([
                'model_type'        => 'App\Models\Customer\CustomerPayment',
                'model_id'          => $payment -> id,
                'case'              => 'in',
                'payment_type_id'   => $payment -> payment_type_id,
                'amount'            => $payment -> amount,
                'created_by'        => $this -> getAuthUserId(),
            ]));

            // STORE ACTION
            event(new ActionHappened('customer payment approved: '.$payment_id, $payment, $this -> getAuthUserId()));
            return $payment;
        });
    }

    public function paymentsDelete($payment_id) {
        $payment = CustomerPayment::notApproved() -> where('id', $payment_id) -> first();
        $payment -> delete();
        event(new ActionHappened('customer payment deleted: '.$payment_id, $payment, $this -> getAuthUserId()));
        return $payment;
    }

    public function paymentsShow($payment_id) {
        return CustomerPayment::withMoneyCourier() -> withCustomer() -> find($payment_id);
    }


    /* *********************************************
    * ************** PRIVATE HELPERS **************
    * *********************************************/
    // STORING THE BRANCHES
    private function storeBranches ($customer_id, $branches) {
        // ADDING THE BRANCH
        foreach ($branches as $branch) {
            $stored_branch = CustomerBranch::create([
                'customer_id' => $customer_id,
                'address'   => $branch['address'],
                'discount'  => $branch['discount'],
                'notes'     => $branch['notes'],
                'created_by'=> $this->getAuthUserId(),
            ]);
            // ADDING BRANCH CONTACTS
            foreach($branch['contacts'] as $contact)
                $stored_branch -> contacts() -> create([
                    'customer_branch_id' => $stored_branch -> id,
                    'name'          => $contact['name'],
                    'phone_number'  => $contact['phone_number'],
                    'position_id'   => $contact['position_id'],
                    'created_by'    => $this -> getAuthUserId(),
                ]);

            // ADDING THE SELLERS
            $stored_branch -> sellers() -> attach($branch['sellers_id']);
        }
    }

    /**
     * @param $sort_by
     * @param $sort_type
     * @return array
     */
    private function setSorting ($sort_by, $sort_type) {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}
