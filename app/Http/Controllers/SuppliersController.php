<?php

namespace App\Http\Controllers;

use App\Http\Requests\Suppliers\SuppliersPaymentRequest;
use App\Models\Supplier\SupplierPayment;
use App\Repositories\Contracts\SupplierRepository;
use App\Http\Requests\TableSearchRequest;
use App\Http\Requests\Suppliers\SuppliersRequest;
use App\Models\Supplier\Supplier;
use Illuminate\Http\Request;
use Response;

class SuppliersController extends Controller
{
    protected $model;
    public function __construct(SupplierRepository $suppliers) {
        $this -> model = $suppliers;
    }

    public function index (Request $request) {
        // TESTED
        return Response::json($this -> model -> getAllActiveSuppliers($request->all()));
    }

    public function search (TableSearchRequest $request) {
        // TESTED
        $getSuppliers = $this -> model -> getSuppliersSearchResult($request -> all());
        return Response::json($getSuppliers);
    }

    public function show ($supplier_id) {
        // TESTED
        return Response::json($this -> model -> showSupplier($supplier_id));
    }

    public function store (SuppliersRequest $request) {
        // TESTED
        return Response::json($this -> model -> addSupplier($request));
    }

    public function edit ($supplier_id) {
        // TESTED
        $edited_supplier = $this -> model -> editSupplier($supplier_id);
        return Response::json($edited_supplier);
    }

    public function update (SuppliersRequest $request, $supplier_id) {
        // TESTED
        $updated_supplier = $this -> model -> updateSupplier($request, $supplier_id);
        return Response::json($updated_supplier);
    }

    public function delete (Supplier $supplier) {
        // TESTED
        $delete_supplier = $this -> model -> deleteSupplier($supplier);
        return Response::json($delete_supplier);
    }

    public function restore ($supplier_id) {
        // TESTED
        $restore_supplier = $this -> model -> restoreSupplier($supplier_id);
        return Response::json($restore_supplier);
    }

    public function addresses ($supplier_id) {
        return Response::json($this -> model -> addresses($supplier_id));
    }

    /* *********************************************
     * ************* SUPPLIER INVOICES *************
     * *********************************************/
    // INVOICES
    public function invoices ($supplier_id) {
        return Response::json($this -> model -> invoices($supplier_id));
    }

    // INVOICES SEARCH
    public function invoicesSearch (TableSearchRequest $request, $supplier_id) {
        return Response::json($this -> model -> invoicesSearch($request -> input('query'), $supplier_id));
    }

    // INVOICES TOTAL
    public function credit ($supplier_id) {
        return Response::json($this -> model -> credit($supplier_id));
    }

    /* *********************************************
     * ************* SUPPLIER PAYMENTS *************
     * *********************************************/
    public function payments () {
        return Response::json($this -> model -> payments());
    }

    public function paymentsSearch (TableSearchRequest $request) {
        return Response::json($this -> model -> paymentsSearch($request -> input('query')));
    }

    public function paymentsAdd (SuppliersPaymentRequest $request) {
        return Response::json($this -> model -> paymentsAdd($request));
    }

    public function paymentsDelete ($payment_id) {
        return Response::json($this -> model -> paymentsDelete($payment_id));
    }

    public function paymentsApprove ($payment_id) {
        return Response::json($this -> model -> paymentsApprove($payment_id));
    }

    public function paymentsShow ($payment_id) {
        return Response::json($this -> model -> paymentsShow($payment_id));
    }
}
