<?php

namespace App\Repositories\Contracts;

interface ProductRepository {
    // RETURN ALL THE ACTIVE PRODUCTS ONLY..
    public function getAllActiveProducts();

    // SHOW FULL PRODUCT DETAILS
    public function showProductDetails($product_id);

    // SEARCH PRODUCTS
    public function getProductsSearchResult($q, $category_id);

    // GET PRODUCTS REQUIREMENTS FOR CREATING A NEW PRODUCT
    public function getProductRequirements();

    // CATEGORY SUBCATEGORY FILTERING
    public function categorySubcategoryFiltering($filtering);

    // ADD NEW PRODUCT
    public function storeProduct($request);

    // ADD NEW IMAGE TO EXISTING PRODUCT
    public function storeNewImageToExistingProduct($request);

    // REMOVE IMAGE FROM EXISTING PRODUCT
    public function removeImageFromExistingProduct($product_id, $image_id);

    // EDIT PRODUCT
    public function editProduct($product_id);

    // UPDATE PRODUCT
    public function updateProduct($request, $product_id);

    // DELETE PRODUCT
    public function deleteProduct($product);

    // RESTORE PRODUCT
    public function restoreProduct($product_id);
}