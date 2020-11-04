<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('import_invoices')->name('import_invoices.')->group(function () {
        // GET ALL IMPORT_INVOICES
        Route::get('/', 'ImportInvoicesController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // SEARCH IMPORT_INVOICE
        Route::get('/search', 'ImportInvoicesController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // IMPORT INVOICE REQUIREMENTS FOR ADDING A NEW IMPORT INVOICE
        Route::get('/add', 'ImportInvoicesController@add')
            -> name('add')
            -> middleware(['role:super_admin']);
        // ADD A NEW IMPORT_INVOICE
        Route::post('/store/invoice', 'ImportInvoicesController@storeInvoice')
            -> name('store.invoice')
            -> middleware(['role:super_admin']);
        Route::post('/store/invoice_products', 'ImportInvoicesController@storeInvoiceProducts')
            -> name('store.invoice_products')
            -> middleware(['role:super_admin']);
        // REMOVE PRODUCT FROM EXPORT_INVOICE
        Route::delete('/{invoice_id}/remove/{purchase_product_id}', 'ImportInvoicesController@removeProductFromInvoice')
            -> name('remove_product')
            -> middleware(['role:super_admin']);

        // SHOW PRODUCT DETAILS
        Route::get('/show/{import_id}', 'ImportInvoicesController@show')
            -> name('show')
            -> middleware(['role:super_admin']);
        // EDIT A IMPORT_INVOICE
        Route::get('/edit/{import_invoice_id}', 'ImportInvoicesController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE IMPORT_INVOICE
        Route::patch('/update/{import_invoice_id}', 'ImportInvoicesController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // SOFT DELETE IMPORT_INVOICE
        Route::delete('/delete/{import_invoice_id}', 'ImportInvoicesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED IMPORT_INVOICE
        Route::get('/restore/{import_invoice_id}', 'ImportInvoicesController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
        // APPROVE INVOICE
        Route::patch('/{import_invoice_id}/approve', 'ImportInvoicesController@approve')
            -> name('approve')
            -> middleware(['role:super_admin']);
    });
});
