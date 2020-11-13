<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('reports/sales/')->name('reports.sales.')->group(function () {
        // GET ALL BRANCH SALES COMPARE
        Route::post('/customer_branch/compare', 'Reports\SalesReportController@customerBranchSalesCompare')
            -> name('customer_branch.sales_compare')
            -> middleware(['role:super_admin']);

        // GET CUSTOMER SALES AND REFUNDS
        Route::post('/customer/sales_and_refunds', 'Reports\SalesReportController@customerSalesAndRefunds')
            -> name('customer.sales_and_refunds')
            -> middleware(['role:super_admin']);

        // GET BRANCH PRODUCTS WITHDRAWALS
        Route::post('/customer_branch/products_withdrawals', 'Reports\SalesReportController@customerBranchProductsWithdrawals')
            -> name('customer_branch.products_withdrawals')
            -> middleware(['role:super_admin']);

        // GET MONTH SALES
        Route::post('/years', 'Reports\SalesReportController@yearlySales')
            -> name('yearly.sales')
            -> middleware(['role:super_admin']);

        // CUSTOMER STATEMENT
        Route::post('/customers_statement', 'Reports\SalesReportController@customersStatement')
            -> name('customers_statement')
            -> middleware(['role:super_admin']);

        // SELLERS PROGRESS
        Route::post('/sellers_progress', 'Reports\SalesReportController@sellersProgress')
            -> name('sellers_progress')
            -> middleware(['role:super_admin|sales']);
    });
});
