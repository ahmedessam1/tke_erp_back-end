<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('refunds')->name('refunds.')->group(function () {
        // GET REFUNDS
        Route::get('/', 'RefundsController@index')
            -> name('index')
            -> middleware(['role:super_admin|accountant|tax']);

        // SEARCH REFUNDS
        Route::get('/search', 'RefundsController@search')
            -> name('search')
            -> middleware(['role:super_admin|accountant|tax']);

        // SHOW REFUNDS
        Route::get('/show/{refund_id}', 'RefundsController@show')
            -> name('show')
            -> middleware(['role:super_admin|accountant|tax']);

        // CREATE REFUNDS
        Route::post('/store/order', 'RefundsController@storeRefundOrder')
            -> name('store.refund_order')
            -> middleware(['role:super_admin|accountant']);
        Route::post('/store/order/products', 'RefundsController@storeRefundOrderProducts')
            -> name('store.refund_order_products')
            -> middleware(['role:super_admin|accountant']);

        // EDIT AND UPDATE
        Route::get('/edit/{refund_id}', 'RefundsController@edit')
            -> name('edit')
            -> middleware(['role:super_admin|accountant|tax']);
        Route::patch('/update/{refund_id}', 'RefundsController@update')
            -> name('update')
            -> middleware(['role:super_admin|accountant']);

        // REMOVE PRODUCT FROM EXPORT_INVOICE
        Route::delete('/{refund_id}/remove/{product_id}', 'RefundsController@removeProductFromRefundOrder')
            -> name('remove_product')
            -> middleware(['role:super_admin|accountant']);

        // APPROVE REFUNDS
        Route::patch('/{refund_id}/approve', 'RefundsController@approve')
            -> name('approve')
            -> middleware(['role:super_admin']);

        // DELETE REFUNDS
        Route::delete('/delete/{refund_id}', 'RefundsController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
    });
});
