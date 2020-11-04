<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('categories')->name('categories.')->group(function () {
        // GET ALL CATEGORIES
        Route::get('/', 'CategoriesController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // ADD A NEW CATEGORY
        Route::post('/store', 'CategoriesController@store')
            -> name('store')
            -> middleware(['role:super_admin']);
        // ADD A NEW SUBCATEGORY
        Route::post('/subcategory/store', 'CategoriesController@storeSubcategory')
            -> name('store_subcategory')
            -> middleware(['role:super_admin']);
        // EDIT A CATEGORY
        Route::get('/edit/{category_id}', 'CategoriesController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE CATEGORY NAME
        Route::patch('/update/{category_id}', 'CategoriesController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // DELETE SUBCATEGORY
        Route::delete('/delete/subcategory/{subcategory_id}', 'CategoriesController@deleteSubcategory')
            -> name('delete_subcategory')
            -> middleware(['role:super_admin']);
        // SEARCH CATEGORIES
        Route::get('/search', 'CategoriesController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // SOFT DELETE CATEGORY
        Route::delete('/delete/{category_id}', 'CategoriesController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED CATEGORY
        Route::get('/restore/{category_id}', 'CategoriesController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
        // CATEGORY SUBCATEGORIES
        Route::get('/{category_id}/subcategories', 'CategoriesController@subcategories')
            -> name('subcategories'); // DOES NOT NEED PERMISSION
    });
});
