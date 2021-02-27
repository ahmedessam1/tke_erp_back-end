<?php
Route::middleware(['auth:api', 'tenant'])->group(function () {
    Route::prefix('general')->name('general.')->group(function () {
        // ATTACH FILES TO INVOICES AND OTHERS
        Route::post('/attach_file', 'GeneralController@attachFileToModel')
            -> name('attach_file')
            -> middleware(['role:super_admin']);
        Route::delete('/attach_file/delete/{id}', 'GeneralController@deleteFileFromModel')
            -> name('attach_file.delete')
            -> middleware(['role:super_admin']);
    });
});
