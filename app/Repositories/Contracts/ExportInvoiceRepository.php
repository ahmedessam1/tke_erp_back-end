<?php

namespace App\Repositories\Contracts;

interface ExportInvoiceRepository {
    // RETURN ALL THE ACTIVE EXPORT INVOICES ONLY..
    public function getAllActiveExportInvoices($request);

    // SEARCH EXPORT INVOICES
    public function getExportInvoicesSearchResult($request);

    // STORE INVOICE
    public function storeInvoice($request);

    // STORE INVOICE PRODUCTS
    public function storeInvoiceProducts($request);

    // REMOVE PRODUCT FROM EXPORT INVOICE
    public function removeProductFromInvoice($invoice_id, $sold_product_id);

    // SHOW EXPORT INVOICE DETAILS
    public function showExportInvoiceDetails($export_invoice_id);

    // EDIT EXPORT INVOICE
    public function editExportInvoice($export_invoice_id);

    // UPDATE EXPORT INVOICE
    public function updateExportInvoice($request, $export_invoice_id);

    // DELETE EXPORT INVOICE
    public function deleteExportInvoice($export_invoice_id);

    // RESTORE EXPORT INVOICE
    public function restoreExportInvoice($export_invoice_id);

    // APPROVE EXPORT INVOICE
    public function approveExportInvoice($export_invoice_id);

    // INVOICES PER USER
    public function invoicesPerUser($request);

    // INVOICES PER USER SEARCH
    public function invoicesPerUserSearch($request);
}