<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('customers')->name('customers.')->group(function () {
        // GET ALL CUSTOMERS
        Route::get('/', 'CustomersController@index')
            -> name('index')
            -> middleware(['role:super_admin']);

        // SEARCH ALL CUSTOMERS
        Route::get('/search', 'CustomersController@search')
            -> name('search')
            -> middleware(['role:super_admin']);

        // SHOW CUSTOMER INFO
        Route::get('/{customer_id}', 'CustomersController@show')
            -> name('show')
            -> middleware(['role:super_admin']);

        // SHOW CUSTOMER BRANCH INFO
        Route::get('/branch/{customer_branch_id}', 'CustomersController@showBranch')
            -> name('show_branch')
            -> middleware(['role:super_admin']);

        // STORE CUSTOMER
        Route::post('/store', 'CustomersController@store')
            -> name('store')
            -> middleware(['role:super_admin']);

        // EDIT CUSTOMER
        Route::get('/edit/{customer_id}', 'CustomersController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);

        // UPDATE CUSTOMER
        Route::patch('/update/{customer_id}', 'CustomersController@update')
            -> name('update')
            -> middleware(['role:super_admin']);

        // ADD CUSTOMER BRANCH
        Route::post('/branch/add', 'CustomersController@addBranch')
            -> name('add_branch')
            -> middleware(['role:super_admin']);

        // DELETE CUSTOMER BRANCH
        Route::delete('/branch/delete/{customer_branch_id}', 'CustomersController@deleteBranch')
            -> name('delete_branch')
            -> middleware(['role:super_admin']);

        // DELETE CUSTOMER
        Route::delete('/delete/{customer_id}', 'CustomersController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);

        // CUSTOMER BRANCH SELLERS
        Route::get('/{customer_id}/sellers', 'CustomersController@sellers')
            -> name('sellers');


        /* *********************************************
         * ******* CUSTOMER INVOICES AND PAYMENTS ******
         * *********************************************/
        // INVOICES
        Route::get('/branch/{customer_branch_id}/invoices', 'CustomersController@invoices')
            -> name('invoices')
            -> middleware(['role:super_admin']);
        Route::get('/{customer_branch_id}/invoices/list', 'CustomersController@invoicesList')
            -> name('invoices_list')
            -> middleware(['role:super_admin']);
        Route::get('/branch/{customer_branch_id}/invoices/search', 'CustomersController@invoicesSearch')
            -> name('invoices.search')
            -> middleware(['role:super_admin']);
        // INVOICES TOTAL
        Route::get('/{customer_branch_id}/credit', 'CustomersController@credit')
            -> name('credit')
            -> middleware(['role:super_admin']);


        /* *********************************************
         * ************* CUSTOMER PAYMENTS *************
         * *********************************************/
        Route::get('/payments/list', 'CustomersController@payments')
            -> name('payments')
            -> middleware(['role:super_admin']);
        Route::get('/payments/list/search', 'CustomersController@paymentsSearch')
            -> name('payments.search')
            -> middleware(['role:super_admin']);
        Route::post('/payments/add', 'CustomersController@paymentsAdd')
            -> name('payments.add')
            -> middleware(['role:super_admin']);
        Route::get('/payments/{payment_id}/approve', 'CustomersController@paymentsApprove')
            -> name('payments.approve')
            -> middleware(['role:super_admin']);
        Route::delete('/payments/delete/{payment_id}', 'CustomersController@paymentsDelete')
            -> name('payments.delete')
            -> middleware(['role:super_admin']);
        Route::get('/payments/show/{payment_id}', 'CustomersController@paymentsShow')
            -> name('payments.show')
            -> middleware(['role:super_admin']);


        /* *********************************************
         * *************** CUSTOMER LIST ***************
         * *********************************************/
        Route::get('/price_list/customers', 'CustomersController@priceListCustomers')
            -> name('price_list.customers')
            -> middleware(['role:super_admin']);
        // PRICE LIST CUSTOMERS SEARCH
        Route::get('/price_list/customers/search', 'CustomersController@priceListCustomersSearch')
            -> name('price_list.customers.search')
            -> middleware(['role:super_admin']);
        Route::post('/price_list/add_product', 'CustomersController@priceListAddProduct')
            -> name('price_list.add_product')
            -> middleware(['role:super_admin']);
        Route::get('/price_list/export', 'CustomersController@priceListExport')
            -> name('price_list.add_product')
            -> middleware(['role:super_admin']);


        /* *********************************************
         * ************* CUSTOMER CONTRACT *************
         * *********************************************/
        Route::get('/contracts/list', 'CustomersController@contractIndex')
            -> name('contracts.index')
            -> middleware(['role:super_admin']);
        Route::get('/contracts/list/search', 'CustomersController@contractSearch')
            -> name('contracts.search')
            -> middleware(['role:super_admin']);
        Route::post('/contracts/store', 'CustomersController@contractStore')
            -> name('contracts.store')
            -> middleware(['role:super_admin']);
        Route::delete('/contracts/delete/{item_id}', 'CustomersController@contractDelete')
            -> name('contracts.delete')
            -> middleware(['role:super_admin']);
    });
});
