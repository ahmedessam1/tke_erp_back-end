<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
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
            -> middleware(['role:super_admin|accountant|digital_marketing']);
        Route::post('/store/invoice_products', 'ExportInvoicesController@storeInvoiceProducts')
            -> name('store.invoice_products')
            -> middleware(['role:super_admin|accountant|digital_marketing']);

        // REMOVE PRODUCT FROM EXPORT_INVOICE
        Route::delete('/{invoice_id}/remove/{sold_product_id}', 'ExportInvoicesController@removeProductFromInvoice')
            -> name('remove_product')
            -> middleware(['role:super_admin|accountant|digital_marketing']);

        // SHOW INVOICE DETAILS
        Route::get('/show/{export_invoice_id}', 'ExportInvoicesController@show')
            -> name('show')
            -> middleware(['role:super_admin|sales|accountant|tax|digital_marketing']);
        // EDIT A EXPORT_INVOICE
        Route::get('/edit/{export_invoice_id}', 'ExportInvoicesController@edit')
            -> name('edit')
            -> middleware(['role:super_admin|accountant|digital_marketing']);
        // UPDATE EXPORT_INVOICE
        Route::patch('/update/{export_invoice_id}', 'ExportInvoicesController@update')
            -> name('update')
            -> middleware(['role:super_admin|accountant|digital_marketing']);
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
            -> middleware(['role:super_admin|sales|digital_marketing']);
        // INVOICES PER USER SEARCH
        Route::get('/per_user/search', 'ExportInvoicesController@invoicesPerUserSearch')
            -> name('per_user.search')
            -> middleware(['role:super_admin|sales|digital_marketing']);
        // UPDATE PRODUCT PURCHASE PRICE IN EXPORT_INVOICE
        Route::get('/update/selling_price/{product_row_id}/{new_price}', 'ExportInvoicesController@updateProductSellingPriceInInvoice')
            -> name('update_selling_price')
            -> middleware(['role:super_admin|accountant|digital_marketing']);

        // REPORTS
        // INVOICE PROFIT
        Route::get('/reports/profit/{item_id}', 'ExportInvoicesController@reportProfit')
            -> name('report.profit')
            -> middleware(['role:super_admin']);
    });
});
