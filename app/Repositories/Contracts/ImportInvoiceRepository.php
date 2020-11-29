<?php

namespace App\Repositories\Contracts;

interface ImportInvoiceRepository {
    // RETURN ALL THE ACTIVE IMPORT INVOICES ONLY..
    public function getAllActiveImportInvoices($request);

    // SEARCH IMPORT INVOICES
    public function getImportInvoicesSearchResult($request);

    // REQUIREMENTS IMPORT INVOICES
    public function getImportInvoiceRequirements();

    // STORE INVOICE
    public function storeInvoice($request);

    // STORE INVOICE PRODUCTS
    public function storeInvoiceProducts($request);

    // REMOVE PRODUCT FROM EXPORT INVOICE
    public function removeProductFromImportInvoice($invoice_id, $purchase_product_id);

    // SHOW IMPORT INVOICE DETAILS
    public function showImportInvoiceDetails($import_invoice_id);

    // EDIT IMPORT INVOICE
    public function editImportInvoice($import_invoice_id);

    // UPDATE IMPORT INVOICE
    public function updateImportInvoice($request, $import_invoice_id);

    // DELETE IMPORT INVOICE
    public function deleteImportInvoice($import_invoice);

    // RESTORE IMPORT INVOICE
    public function restoreImportInvoice($import_invoice_id);

    // APPROVE IMPORT INVOICE
    public function approveImportInvoice($import_invoice_id);

    public function updateProductPurchasePriceInInvoice($product_row_id, $new_price);
}
