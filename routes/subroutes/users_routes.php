<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        // GET ALL USERS
        Route::get('/', 'UsersController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // DEACTIVATE USER
        Route::delete('/deactivate/{user}', 'UsersController@deactivate')
            -> name('deactivate')
            -> middleware(['role:super_admin']);
        // REACTIVATE USER
        Route::get('/reactivate/{user}', 'UsersController@reactivate')
            -> name('reactivate')
            -> middleware(['role:super_admin']);
        // SEARCH USERS
        Route::get('/search', 'UsersController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
    });
});
