<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('initiatory')->name('initiatory.')->group(function () {
        // PRODUCT CREDITS
        Route::get('/product/credits/index', 'InitiatoryController@productCreditsIndex')
            -> name('product.credits.index')
            -> middleware(['role:super_admin']);
        Route::get('/product/credits/search', 'InitiatoryController@productCreditsSearch')
            -> name('product.credits.search')
            -> middleware(['role:super_admin']);
        Route::post('/product/credits/store', 'InitiatoryController@productCreditsStore')
            -> name('product.credits.store')
            -> middleware(['role:super_admin']);
        Route::delete('/product/credits/delete/{product_credit_id}', 'InitiatoryController@productCreditsDelete')
            -> name('product.credits.delete')
            -> middleware(['role:super_admin']);


        // SUPPLIER CREDITS
        Route::get('/supplier/credits/index', 'InitiatoryController@supplierCreditsIndex')
            -> name('supplier.credits.index')
            -> middleware(['role:super_admin']);
        Route::get('/supplier/credits/search', 'InitiatoryController@supplierCreditsSearch')
            -> name('supplier.credits.search')
            -> middleware(['role:super_admin']);
        Route::post('/supplier/credits/store', 'InitiatoryController@supplierCreditsStore')
            -> name('supplier.credits.store')
            -> middleware(['role:super_admin']);
        Route::delete('/supplier/credits/delete/{supplier_credit_id}', 'InitiatoryController@supplierCreditsDelete')
            -> name('supplier.credits.delete')
            -> middleware(['role:super_admin']);


        // CUSTOMER CREDITS
        Route::get('/customer/credits/index', 'InitiatoryController@customerCreditsIndex')
            -> name('customer.credits.index')
            -> middleware(['role:super_admin']);
        Route::get('/customer/credits/search', 'InitiatoryController@customerCreditsSearch')
            -> name('customer.credits.search')
            -> middleware(['role:super_admin']);
        Route::post('/customer/credits/store', 'InitiatoryController@customerCreditsStore')
            -> name('customer.credits.store')
            -> middleware(['role:super_admin']);
        Route::delete('/customer/credits/delete/{customer_credit_id}', 'InitiatoryController@customerCreditsDelete')
            -> name('customer.credits.delete')
            -> middleware(['role:super_admin']);
    });
});
