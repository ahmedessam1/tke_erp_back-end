<?php

//AUTHENTICATION ROUTES
Route::post('/login', 'AuthController@login') -> name('login');

Route::middleware(['auth:api'])->group(function () {
    // USERS ROUTES
    // USER INFO
    Route::get('/logged/user/info', 'AuthController@getUserDetails')
        -> name('logged.user.info');
    // USER REGISTER
    Route::post('/register', 'AuthController@register')
        -> name('register')
        -> middleware(['role:super_admin']);
    // ALL PERMISSIONS
    Route::get('/permissions', 'AuthController@permissions')
        -> name('permissions')
        -> middleware(['role:super_admin']);


    // ADDING PRODUCTS CREDITS
    Route::post('/import/products/credits', 'ImportDataController@productCredits');
});
