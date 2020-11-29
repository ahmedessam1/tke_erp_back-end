<?php

namespace App\Http\Controllers;


use App\Http\Requests\ImportInvoices\InvoiceProductsStoreRequest;
use App\Http\Requests\ImportInvoices\InvoiceStoreRequest;
use App\Repositories\Contracts\ImportInvoiceRepository;
use App\Http\Requests\ImportInvoicesRequest;
use App\Http\Requests\TableSearchRequest;
use Illuminate\Http\Request;
use Response;

class ImportInvoicesController extends Controller
{
    protected $model;
    public function __construct(ImportInvoiceRepository $import_invoices) {
        $this->model = $import_invoices;
    }

    public function index (Request $request) {
        // TESTED....
        return Response::json($this->model->getAllActiveImportInvoices($request->all()));
    }

    public function search (TableSearchRequest $request) {
        // TESTED....
        $getImportInvoices = $this->model->getImportInvoicesSearchResult($request->all());
        return Response::json($getImportInvoices);
    }

    public function add () {
        return Response::json($this->model->getImportInvoiceRequirements());
    }

    public function storeInvoice (InvoiceStoreRequest $request) {
        return Response::json($this->model->storeInvoice($request));
    }

    public function storeInvoiceProducts (InvoiceProductsStoreRequest $request) {
        return Response::json($this->model->storeInvoiceProducts($request));
    }

    public function removeProductFromInvoice ($invoice_id, $purchase_product_id) {
        return Response::json($this->model->removeProductFromImportInvoice($invoice_id, $purchase_product_id));
    }

    public function show ($import_invoice_id) {
        // TESTED....
        return Response::json($this->model->showImportInvoiceDetails($import_invoice_id));
    }

    public function edit ($import_invoice_id) {
        // TESTED....
        return Response::json($this->model->editImportInvoice($import_invoice_id));
    }

    public function update (ImportInvoicesRequest $request, $import_invoice_id) {
        // TESTED....
        return Response::json($this->model->updateImportInvoice($request, $import_invoice_id));
    }

    public function delete ($import_invoice_id) {
        // TESTED....
        $delete_import_invoice = $this->model->deleteImportInvoice($import_invoice_id);
        return Response::json($delete_import_invoice);
    }

    public function restore ($import_invoice_id) {
        // TESTED....
        return Response::json($this->model->restoreImportInvoice($import_invoice_id));
    }

    public function approve (Request $request, $import_invoice_id) {
        // TESTED....
        return Response::json($this->model->approveImportInvoice($import_invoice_id));
    }

    public function updateProductPurchasePriceInInvoice ($product_row_id, $new_price) {
        return Response::json($this->model->updateProductPurchasePriceInInvoice($product_row_id, $new_price));
    }
}
