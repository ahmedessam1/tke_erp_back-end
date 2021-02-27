<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('exports/excel')->name('exports.excel.')->group(function () {
        // EXPORT PRODUCT CREDITS TO EXCEL
        Route::get('/invoices/{type}/{invoice_id}', 'Exports\InvoicesController@exportExportInvoiceToExcel')
            -> name('export_invoice'); // PERMISSION IN THE CONTROLLER
    });
});
