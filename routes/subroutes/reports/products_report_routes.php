<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('reports/products/')->name('reports.products.')->group(function () {
        // EXPORT PRODUCT CREDITS TO EXCEL
        Route::get('/credits_export', 'Reports\ProductsReportController@exportProductsCredit')
            -> name('credits_export')
            -> middleware(['role:super_admin|sales']);

        // SHOW THE PRODUCT HISTORY
        Route::post('/history', 'Reports\ProductsReportController@productHistory')
            -> name('history')
            -> middleware(['role:super_admin']);

        // SHOW THE TOP SOLD PRODUCTS BY AMOUNT
        Route::post('/top_sold/amount', 'Reports\ProductsReportController@topSoldByAmount')
            -> name('top_sold.amount')
            -> middleware(['role:super_admin']);

        // SHOW THE TOP SOLD PRODUCTS REPEATEDLY
        Route::post('/top_sold/repeatedly', 'Reports\ProductsReportController@topSoldRepeatedly')
            -> name('top_sold.repeatedly')
            -> middleware(['role:super_admin']);

        // SHOW THE TOP SOLD PRODUCTS PROFITABLE/UNPROFITABLE
        Route::post('/top_sold/profit', 'Reports\ProductsReportController@topSoldProfit')
            -> name('top_sold.profit')
            -> middleware(['role:super_admin']);

        // EXPORT SUPPLIER PRODUCT CREDITS TO EXCEL
        Route::post('/supplier/products/credits_export', 'Reports\ProductsReportController@exportSupplierProductsCredit')
            -> name('supplier.products.credits_export')
            -> middleware(['role:super_admin']);

        // CATEGORY SALES WITHIN TIME RANGE
        Route::post('/sales', 'Reports\ProductsReportController@sales')
            -> name('sales')
            -> middleware(['role:super_admin']);
    });
});
