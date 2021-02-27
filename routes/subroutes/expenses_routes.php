<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('expenses')->name('expenses.')->group(function () {
        // GET ALL
        Route::get('/', 'ExpensesController@index')
            -> name('index')
            -> middleware(['role:super_admin']);

        // SEARCH
        Route::get('/search', 'ExpensesController@search')
            -> name('search')
            -> middleware(['role:super_admin']);

        // SHOW
        Route::get('/{item_id}', 'ExpensesController@show')
            -> name('show')
            -> middleware(['role:super_admin']);

        // ADD NEW
        Route::post('/store', 'ExpensesController@store')
            ->name('store')
            ->middleware(['role:super_admin']);

        // UPDATE
        Route::patch('/update/{item_id}', 'ExpensesController@update')
            -> name('update')
            -> middleware(['role:super_admin']);

        // APPROVE
        Route::patch('/{item_id}/approve', 'ExpensesController@approve')
            -> name('approve')
            -> middleware(['role:super_admin']);

        // SOFT DELETE
        Route::delete('/delete/{item_id}', 'ExpensesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
    });
});
