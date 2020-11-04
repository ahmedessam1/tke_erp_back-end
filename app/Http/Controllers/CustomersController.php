<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerAddBranchRequest;
use App\Http\Requests\Customer\CustomersPaymentRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Repositories\Contracts\CustomerRepository;
use App\Http\Requests\Customer\CustomerRequest;
use App\Http\Requests\TableSearchRequest;
use Illuminate\Http\Request;
use Response;

class CustomersController extends Controller
{
    protected $model;
    public function __construct(CustomerRepository $customers) {
        $this->model = $customers;
    }

    public function index (Request $request) {
        return Response::json($this->model->getAllActiveCustomers($request->all()));
    }

    public function search (TableSearchRequest $request) {
        return Response::json($this->model->searchCustomers($request->all()));
    }

    public function show ($customer_id) {
        return Response::json($this->model->showCustomer($customer_id));
    }

    public function showBranch ($customer_branch_id) {
        return Response::json($this->model->showCustomerBranch($customer_branch_id));
    }

    public function store (CustomerRequest $request) {
        return Response::json($this->model->addCustomer($request));
    }

    public function edit ($customer_id) {
        return Response::json($this->model->editCustomer($customer_id));
    }

    public function update (CustomerUpdateRequest $request, $customer_id) {
        return Response::json($this->model->updateCustomer($request, $customer_id));
    }

    public function addBranch (CustomerAddBranchRequest $request) {
        return Response::json($this->model->addBranch($request));
    }

    public function deleteBranch ($customer_branch_id) {
        return Response::json($this->model->deleteCustomerBranch($customer_branch_id));
    }

    public function delete ($customer_id) {
        return Response::json($this->model->deleteCustomer($customer_id));
    }

    public function sellers ($customer_id) {
        return Response::json($this->model->sellers($customer_id));
    }

    /* *********************************************
     * ************ CUSTOMERS INVOICES *************
     * *********************************************/
    // INVOICES
    public function invoices ($customer_branch_id) {
        return Response::json($this->model->invoices($customer_branch_id));
    }

    // INVOICES LIST
    public function invoicesList ($customer_branch_id) {
        return Response::json($this->model->invoicesList($customer_branch_id));
    }

    // INVOICES SEARCH
    public function invoicesSearch (TableSearchRequest $request, $customer_branch_id) {
        return Response::json($this->model->invoicesSearch($request->input('query'), $customer_branch_id));
    }

    // INVOICES TOTAL
    public function credit ($customer_branch_id) {
        return Response::json($this->model->credit($customer_branch_id));
    }

    /* *********************************************
     * ************* CUSTOMER PAYMENTS *************
     * *********************************************/
    public function payments () {
        return Response::json($this->model->payments());
    }

    public function paymentsSearch (TableSearchRequest $request) {
        return Response::json($this->model->paymentsSearch($request->input('query')));
    }

    public function paymentsAdd (CustomersPaymentRequest $request) {
        return Response::json($this->model->paymentsAdd($request));
    }

    public function paymentsDelete ($payment_id) {
        return Response::json($this->model->paymentsDelete($payment_id));
    }

    public function paymentsApprove ($payment_id) {
        return Response::json($this->model->paymentsApprove($payment_id));
    }

    public function paymentsShow ($payment_id) {
        return Response::json($this->model->paymentsShow($payment_id));
    }
}
