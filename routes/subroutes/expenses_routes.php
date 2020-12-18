<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('expenses')->name('expenses.')->group(function () {
        // GET ALL EXPENSES
        Route::get('/', 'ExpensesController@index')
            -> name('index')
            -> middleware(['role:super_admin']);

        // SEARCH EXPENSES
        Route::get('/search', 'ExpensesController@search')
            -> name('search')
            -> middleware(['role:super_admin']);

        // SHOW EXPENSES
        Route::get('/{item_id}', 'ExpensesController@show')
            -> name('show')
            -> middleware(['role:super_admin']);

        // ADD NEW EXPENSES
        Route::post('/store', 'ExpensesController@store')
            ->name('store')
            ->middleware(['role:super_admin']);

        // SOFT DELETE EXPENSES
        Route::delete('/delete/{item_id}', 'ExpensesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
    });
});
