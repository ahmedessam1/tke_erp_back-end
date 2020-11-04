<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('product_dismiss_orders')->name('products_dismiss_order.')->group(function () {
        // GET ALL PRODUCT DISMISS ORDERS
        Route::get('/', 'ProductDismissOrdersController@index')
            -> name('index')
            -> middleware(['role:super_admin']);

        // SEARCH PRODUCT DISMISS ORDER
        Route::get('/search', 'ProductDismissOrdersController@search')
            -> name('search')
            -> middleware(['role:super_admin']);

        // SHOW PRODUCT DISMISS ORDER DETAILS
        Route::get('/show/{product_dismiss_order_id}', 'ProductDismissOrdersController@show')
            -> name('show')
            -> middleware(['role:super_admin']);

        // CREATE NEW PRODUCT DISMISS ORDER
        Route::post('/store', 'ProductDismissOrdersController@store')
            -> name('store')
            -> middleware(['role:super_admin']);

        // SOFT DELETE PRODUCT DISMISS ORDER
        Route::delete('/delete/{product_dismiss_order_id}', 'ProductDismissOrdersController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);

        // APPROVE PRODUCT DISMISS ORDER
        Route::get('/{product_dismiss_order_id}/approve', 'ProductDismissOrdersController@approve')
            -> name('approve')
            -> middleware(['role:super_admin']);
    });
});
