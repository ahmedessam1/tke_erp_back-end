<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        // GET ALL SUPPLIERS
        Route::get('/', 'SuppliersController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // SUPPLIER DETAILS
        Route::get('/{supplier_id}', 'SuppliersController@show')
            -> name('show')
            -> middleware(['role:super_admin']);
        // ADD A NEW SUPPLIERS
        Route::post('/store', 'SuppliersController@store')
            -> name('store')
            -> middleware(['role:super_admin']);
        // EDIT A SUPPLIER
        Route::get('/edit/{supplier_id}', 'SuppliersController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE SUPPLIER
        Route::patch('/update/{supplier_id}', 'SuppliersController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // SEARCH SUPPLIERS
        Route::get('/all/search', 'SuppliersController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // SOFT DELETE SUPPLIER
        Route::delete('/delete/{supplier}', 'SuppliersController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED SUPPLIER
        Route::get('/restore/{supplier_id}', 'SuppliersController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);

        // SUPPLIER ADDRESSES
        Route::get('/{supplier_id}/addresses', 'SuppliersController@addresses')
            -> name('addresses')
            -> middleware(['role:super_admin']);

        /* *********************************************
         * ******* SUPPLIER INVOICES AND PAYMENTS ******
         * *********************************************/
        // INVOICES
        Route::get('/{supplier_id}/invoices', 'SuppliersController@invoices')
            -> name('invoices')
            -> middleware(['role:super_admin']);
        Route::get('/{supplier_id}/invoices/search', 'SuppliersController@invoicesSearch')
            -> name('invoices.search')
            -> middleware(['role:super_admin']);
        // INVOICES TOTAL
        Route::get('/{supplier_id}/credit', 'SuppliersController@credit')
            -> name('credit')
            -> middleware(['role:super_admin']);


        /* *********************************************
         * ************* SUPPLIER PAYMENTS *************
         * *********************************************/
        Route::get('/payments/list', 'SuppliersController@payments')
            -> name('payments')
            -> middleware(['role:super_admin']);
        Route::get('/payments/list/search', 'SuppliersController@paymentsSearch')
            -> name('payments.search')
            -> middleware(['role:super_admin']);
        Route::post('/payments/add', 'SuppliersController@paymentsAdd')
            -> name('payments.add')
            -> middleware(['role:super_admin']);
        Route::get('/payments/{payment_id}/approve', 'SuppliersController@paymentsApprove')
            -> name('payments.approve')
            -> middleware(['role:super_admin']);
        Route::delete('/payments/delete/{payment_id}', 'SuppliersController@paymentsDelete')
            -> name('payments.delete')
            -> middleware(['role:super_admin']);
        Route::get('/payments/show/{payment_id}', 'SuppliersController@paymentsShow')
            -> name('payments.show')
            -> middleware(['role:super_admin']);
    });
});
