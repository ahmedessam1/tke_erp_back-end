<?php

namespace App\Repositories\Contracts;

interface CategoryRepository {
    // RETURN ALL THE ACTIVE CATEGORIES ONLY..
    public function getAllActiveCategories();

    // ADD NEW CATEGORY
    public function addCategory($request);

    // ADD NEW CATEGORY
    public function addSubcategory($request);

    // EDIT CATEGORY
    public function editCategory($category_id);

    // UPDATE CATEGORY
    public function updateCategory($request, $category_id);

    // SEARCH CATEGORIES
    public function getCategoriesSearchResult($q);

    // DELETE CATEGORY
    public function deleteCategory($category);

    // RESTORE CATEGORY
    public function restoreCategory($category_id);

    // GET SUBCATEGORIES
    public function getSubcategories($category_id);

    // DELETE SUBCATEGORY
    public function deleteSubcategory($subcategory_id);
}