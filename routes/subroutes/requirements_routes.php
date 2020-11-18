<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('requirements')->name('requirements.')->group(function () {
        // GET PAYMENT METHODS
        Route::get('/users', 'RequirementsController@users')
            -> name('users'); // ROLE IS SET INSIDE


        // GET PAYMENT METHODS
        Route::get('/payment_types', 'RequirementsController@paymentTypes')
            -> name('payment_types')
            -> middleware(['role:super_admins']);

        // GET SUPPLIERS
        Route::get('/suppliers', 'RequirementsController@suppliers')
            -> name('suppliers')
            -> middleware(['role:super_admin']);

        // GET JOB POSITIONS
        Route::get('/positions', 'RequirementsController@positions')
            -> name('positions')
            -> middleware(['role:super_admin']);

        // GET CATEGORIES
        Route::get('/categories', 'RequirementsController@categories')
            -> name('categories'); // ANYONE ON THE SYSTEM CAN SEE THE CATEGORIES

        // GET CATEGORIES
        Route::get('/warehouses', 'RequirementsController@warehouses')
            -> name('warehouses');

        // GET CUSTOMERS
        Route::get('/customers', 'RequirementsController@customers')
            -> name('customers'); // PERMISSION IS ADDED IN THE REPOSITORY

        // GET CUSTOMERS BRANCHES
        Route::get('/customers/branches', 'RequirementsController@customersBranches')
            -> name('customers_branches'); // PERMISSION IS ADDED IN THE REPOSITORY

        // GET PRODUCT DISMISS REASONS
        Route::get('/product_dismiss_reasons', 'RequirementsController@productDismissReasons')
            -> name('product_dismiss_reasons'); // PERMISSION IS ADDED IN THE REPOSITORY
    });
});
