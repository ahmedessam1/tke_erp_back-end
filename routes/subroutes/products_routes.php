<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('products')->name('products.')->group(function () {
        // GET ALL PRODUCTS
        Route::get('/', 'ProductsController@index')
            -> name('index')
            -> middleware(['role:super_admin']);
        // SHOW PRODUCT DETAILS
        Route::get('/show/{product_id}', 'ProductsController@show')
            -> name('show')
            -> middleware(['role:super_admin']);
        // PRODUCT REQUIREMENTS FOR ADDING A NEW PRODUCT
        Route::get('/add', 'ProductsController@add')
            -> name('add')
            -> middleware(['role:super_admin']);
        // ADD A NEW PRODUCT
        Route::post('/store', 'ProductsController@store')
            -> name('store')
            -> middleware(['role:super_admin']);
        // FILTERING PRODUCTS BY CATEGORY AND SUBCATEGORIES
        Route::post('/category/subcategory/filtering', 'ProductsController@categorySubcategoryFiltering')
            -> name('category.subcategory.filtering')
            -> middleware(['role:super_admin']);
        // ADD IMAGE TO PRODUCT
        Route::post('/add/image', 'ProductsController@addImage')
            -> name('add.image')
            -> middleware(['role:super_admin']);
        // REMOVE IMAGE FROM PRODUCT
        Route::get('/remove/image/{product_id}/{image_id}', 'ProductsController@removeImage')
            -> name('remove.image')
            -> middleware(['role:super_admin']);
        // EDIT A PRODUCT
        Route::get('/edit/{product_id}', 'ProductsController@edit')
            -> name('edit')
            -> middleware(['role:super_admin']);
        // UPDATE PRODUCT
        Route::patch('/update/{product_id}', 'ProductsController@update')
            -> name('update')
            -> middleware(['role:super_admin']);
        // SEARCH PRODUCTS
        Route::get('/search', 'ProductsController@search')
            -> name('search')
            -> middleware(['role:super_admin']);
        // SOFT DELETE PRODUCT
        Route::delete('/delete/{product}', 'ProductsController@delete')
            -> name('delete')
            -> middleware(['role:super_admin']);
        // RESTORE SOFT DELETED PRODUCT
        Route::get('/restore/{product_id}', 'ProductsController@restore')
            -> name('restore')
            -> middleware(['role:super_admin']);
        // PRODUCT BARCODE CHECK
        Route::get('/barcode/check', 'ProductsController@barcodeCheck')
            -> name('barcode_check')
            -> middleware(['role:super_admin']);
    });
});
