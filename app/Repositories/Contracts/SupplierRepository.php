<?php

namespace App\Repositories\Contracts;

interface SupplierRepository {
    // RETURN ALL THE ACTIVE SUPPLIERS ONLY..
    public function getAllActiveSuppliers($request);

    // SEARCH SUPPLIERS
    public function getSuppliersSearchResult($request);

    // SUPPLIER DETAILS..
    public function showSupplier($supplier_id);

    // ADD NEW SUPPLIER
    public function addSupplier($request);

    // EDIT SUPPLIER
    public function editSupplier($supplier_id);

    // UPDATE SUPPLIER
    public function updateSupplier($request, $supplier_id);

    // DELETE SUPPLIER
    public function deleteSupplier($supplier);

    // RESTORE SUPPLIER
    public function restoreSupplier($supplier_id);

    // ADDRESSES
    public function addresses($supplier_id);

    /************************************
     * ******** INVOICES SECTION ********
     ***********************************/
    // INVOICES
    public function invoices($supplier_id);

    // INVOICES SEARCH
    public function invoicesSearch($query, $supplier_id);

    // CREDIT
    public function credit($supplier_id);

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
}