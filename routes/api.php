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
    // USER EDIT
    Route::get('/users/edit/{user_id}', 'AuthController@edit')
        -> name('edit')
        -> middleware(['role:super_admin']);
    // USER UPDATE
    Route::patch('/users/update/{user_id}', 'AuthController@update')
        -> name('update')
        -> middleware(['role:super_admin']);
    // USER UPDATE PASSWORD
    Route::patch('/users/update/{user_id}/password', 'AuthController@updatePassword')
        -> name('update_password')
        -> middleware(['role:super_admin']);
    // ALL PERMISSIONS
    Route::get('/roles', 'AuthController@roles')
        -> name('roles')
        -> middleware(['role:super_admin']);


    // ADDING PRODUCTS CREDITS
    Route::post('/import/products/credits', 'ImportDataController@productCredits');
});
