<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('custody')->name('expenses.')->group(function () {
        // GET ALL
        Route::get('/money', 'CustodyController@moneyCustodyIndex')
            -> name('money.index')
            -> middleware(['role:super_admin']);

        // SEARCH
        Route::get('/money/search', 'CustodyController@moneyCustodySearch')
            -> name('money.search')
            -> middleware(['role:super_admin']);

        // SHOW
        Route::get('/money/{item_id}', 'CustodyController@moneyCustodyShow')
            -> name('money.show')
            -> middleware(['role:super_admin']);

        // ADD NEW
        Route::post('/money/store', 'CustodyController@moneyCustodyStore')
            ->name('money.store')
            ->middleware(['role:super_admin']);

        // UPDATE
        Route::patch('/money/update/{item_id}', 'CustodyController@moneyCustodyUpdate')
            -> name('money.update')
            -> middleware(['role:super_admin']);

        // APPROVE
        Route::patch('/money/{item_id}/approve', 'CustodyController@moneyCustodyApprove')
            -> name('money.approve')
            -> middleware(['role:super_admin']);

        // SOFT DELETE
        Route::delete('/money/delete/{item_id}', 'CustodyController@moneyCustodyDelete')
            -> name('money.delete')
            -> middleware(['role:super_admin']);
    });
});
