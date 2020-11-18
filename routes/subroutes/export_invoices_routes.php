<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('export_invoices')->name('export_invoices.')->group(function () {
        // GET ALL EXPORT_INVOICES
        Route::get('/', 'ExportInvoicesController@index')
            -> name('index')
            -> middleware(['role:super_admin|accountant|tax']);
        // SEARCH EXPORT_INVOICE
        Route::get('/search', 'ExportInvoicesController@search')
            -> name('search')
            -> middleware(['role:super_admin|accountant|tax']);

        // ADD A NEW EXPORT_INVOICE
        Route::post('/store/invoice', 'ExportInvoicesController@storeInvoice')
            -> name('store.invoice')
            -> middleware(['role:super_admin|accountant']);
        Route::post('/store/invoice_products', 'ExportInvoicesController@storeInvoiceProducts')
            -> name('store.invoice_products')
            -> middleware(['role:super_admin|accountant']);

        // REMOVE PRODUCT FROM EXPORT_INVOICE
        Route::delete('/{invoice_id}/remove/{sold_product_id}', 'ExportInvoicesController@removeProductFromInvoice')
            -> name('remove_product')
            -> middleware(['role:super_admin|accountant']);

        // SHOW INVOICE DETAILS
        Route::get('/show/{export_invoice_id}', 'ExportInvoicesController@show')
            -> name('show')
            -> middleware(['role:super_admin|sales|accountant|tax']);
        // EDIT A EXPORT_INVOICE
        Route::get('/edit/{export_invoice_id}', 'ExportInvoicesController@edit')
            -> name('edit')
            -> middleware(['role:super_admin|accountant']);
        // UPDATE EXPORT_INVOICE
        Route::patch('/update/{export_invoice_id}', 'ExportInvoicesController@update')
            -> name('update')
            -> middleware(['role:super_admin|accountant']);
        // SOFT DELETE EXPORT_INVOICE
        Route::delete('/delete/{export_invoice_id}', 'ExportInvoicesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED EXPORT_INVOICE
        Route::get('/restore/{export_invoice_id}', 'ExportInvoicesController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
        // APPROVE INVOICE
        Route::patch('/{export_invoice_id}/approve', 'ExportInvoicesController@approve')
            -> name('approve')
            -> middleware(['role:super_admin']);

        // INVOICES PER USER
        Route::get('/per_user', 'ExportInvoicesController@invoicesPerUser')
            -> name('per_user')
            -> middleware(['role:super_admin|sales']);
        // INVOICES PER USER SEARCH
        Route::get('/per_user/search', 'ExportInvoicesController@invoicesPerUserSearch')
            -> name('per_user.search')
            -> middleware(['role:super_admin|sales']);
    });
});
