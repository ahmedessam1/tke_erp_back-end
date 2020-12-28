<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('general')->name('general.')->group(function () {
        // ATTACH FILES TO INVOICES AND OTHERS
        Route::get('/', 'GeneralController@attachFileToModel')
            -> name('attach_file')
            -> middleware(['role:super_admin']);
    });
});
