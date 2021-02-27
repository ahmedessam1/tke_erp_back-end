<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('seasons')->name('seasons.')->group(function () {
        // GET ALL SEASONS
        Route::get('/', 'SeasonsController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // ADD A NEW SEASON
        Route::post('/store', 'SeasonsController@store')
            -> name('store')
            -> middleware(['role:super_admin']);
        // EDIT A SEASON
        Route::get('/edit/{season_id}', 'SeasonsController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE SEASON
        Route::patch('/update/{season_id}', 'SeasonsController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // SEARCH SEASONS
        Route::get('/search', 'SeasonsController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // SOFT DELETE SEASON
        Route::delete('/delete/{season_id}', 'SeasonsController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED SEASON
        Route::get('/restore/{season_id}', 'SeasonsController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
    });
});
