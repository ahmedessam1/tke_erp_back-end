<?php

namespace App\Repositories\Contracts;

interface CustomerRepository {
    // RETURN ALL THE ACTIVE CUSTOMERS
    public function getAllActiveCustomers($request);

    // SEARCH CUSTOMERS
    public function searchCustomers($request);

    // SHOW CUSTOMER BRANCH
    public function showCustomer($customer_id);

    // SHOW CUSTOMER BRANCH
    public function showCustomerBranch($customer_branch_id);

    // STORE CUSTOMER
    public function addCustomer($request);

    // EDIT CUSTOMER
    public function editCustomer($customer_id);

    // UPDATE CUSTOMER
    public function updateCustomer($request, $customer_id);

    // ADD BRANCH
    public function addBranch($request);

    // DELETE CUSTOMER BRANCH
    public function deleteCustomerBranch($customer_branch_id);

    // DELETE CUSTOMER
    public function deleteCustomer($customer_id);

    // CUSTOMER SELLERS
    public function sellers($customer_id);

    /************************************
     * ******** INVOICES SECTION ********
     ***********************************/
    // INVOICES
    public function invoices($customer_branch_id);

    // INVOICES LIST
    public function invoicesList($customer_branch_id);

    // INVOICES SEARCH
    public function invoicesSearch($query, $customer_branch_id);

    // CREDIT
    public function credit($customer_branch_id);

    /************************************
     * ******** PAYMENTS SECTION ********
     ***********************************/
    // PAYMENTS
    public function payments();

    // PAYMENTS SEARCH
    public function paymentsSearch($query);

    // PAYMENTS ADD
    public function paymentsAdd($request);

    // PAYMENTS APPROVE
    public function paymentsApprove($payment_id);

    // PAYMENTS DELETE
    public function paymentsDelete($payment_id);

    // PAYMENTS SHOW
    public function paymentsShow($payment_id);


    /************************************
     * ********* CUSTOMERS LIST *********
     ***********************************/
    public function priceListCustomers($request);
    public function priceListCustomersSearch($request);


    /************************************
     * ****** CUSTOMERS CONTRACTS *******
     ***********************************/
    public function contractIndex($request);
    public function contractSearch($request);
    public function contractStore($request);
    public function contractDelete($item_id);
}
