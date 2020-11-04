<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportInvoices\InvoiceProductsStoreRequest;
use App\Http\Requests\ExportInvoices\InvoiceStoreRequest;
use App\Http\Requests\ExportInvoicesRequest;
use App\Http\Requests\TableSearchRequest;
use App\Models\Invoices\ExportInvoice;
use App\Repositories\Contracts\ExportInvoiceRepository;
use Illuminate\Http\Request;
use Response;

class ExportInvoicesController extends Controller
{
    protected $model;
    public function __construct(ExportInvoiceRepository $export_invoices) {
        $this->model = $export_invoices;
    }

    public function index (Request $request) {
        return Response::json($this->model->getAllActiveExportInvoices($request->all()));
    }

    public function search (TableSearchRequest $request) {
        return Response::json($this->model->getExportInvoicesSearchResult($request->all()));
    }

    public function storeInvoice (InvoiceStoreRequest $request) {
        return Response::json($this->model->storeInvoice($request));
    }

    public function storeInvoiceProducts (InvoiceProductsStoreRequest $request) {
        return Response::json($this->model->storeInvoiceProducts($request));
    }

    public function removeProductFromInvoice ($invoice_id, $sold_product_id) {
        return Response::json($this->model->removeProductFromInvoice($invoice_id, $sold_product_id));
    }

    public function show ($export_invoice_id) {
        return Response::json($this->model->showExportInvoiceDetails($export_invoice_id));
    }

    public function edit ($export_invoice_id) {
        return Response::json($this->model->editExportInvoice($export_invoice_id));
    }

    public function update (ExportInvoicesRequest $request, $export_invoice_id) {
        return Response::json($this->model->updateExportInvoice($request, $export_invoice_id));
    }

    public function delete ($export_invoice_id) {
        return Response::json($this->model->deleteExportInvoice($export_invoice_id));
    }

    public function restore ($export_invoice_id) {
        return Response::json($this->model->restoreExportInvoice($export_invoice_id));
    }

    public function approve (Request $request, $export_invoice_id) {
        return Response::json($this->model->approveExportInvoice($export_invoice_id));
    }

    public function invoicesPerUser (Request $request) {
        return Response::json($this->model->invoicesPerUser($request->all()));
    }

    public function invoicesPerUserSearch (TableSearchRequest $request) {
        return Response::json($this->model->invoicesPerUserSearch($request->all()));
    }
}
