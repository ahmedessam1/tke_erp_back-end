<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        // GET ALL WAREHOUSES
        Route::get('/', 'WarehousesController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // LIST ALL WAREHOUSES
        Route::get('/listing', 'WarehousesController@listing')
            -> name('list')
            -> middleware(['role:super_admin']);
        // ADD A NEW WAREHOUSE
        Route::post('/store', 'WarehousesController@store')
            -> name('store')
            -> middleware(['role:super_admin']);
        // EDIT A WAREHOUSE
        Route::get('/edit/{warehouse_id}', 'WarehousesController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE WAREHOUSE
        Route::patch('/update/{warehouse_id}', 'WarehousesController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // SEARCH WAREHOUSES
        Route::get('/search', 'WarehousesController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // SOFT DELETE WAREHOUSE
        Route::delete('/delete/{warehouse_id}', 'WarehousesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED WAREHOUSE
        Route::get('/restore/{warehouse_id}', 'WarehousesController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
    });
});
