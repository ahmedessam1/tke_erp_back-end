<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubcategoriesRequest;
use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\CategoriesRequest;
use Response;

class CategoriesController extends Controller
{
    protected $model;
    public function __construct(CategoryRepository $categories) {
        $this -> model = $categories;
    }

    public function index () {
        // TESTED....
        return Response::json($this -> model -> getAllActiveCategories());
    }

    public function search (TableSearchRequest $request) {
        // TESTED....
        return Response::json($this -> model -> getCategoriesSearchResult($request -> input('query')));
    }

    public function store (CategoriesRequest $request) {
        // TESTED....
        return Response::json($this -> model -> addCategory($request));
    }

    public function storeSubcategory (SubcategoriesRequest $request) {
        // TESTED....
        return Response::json($this -> model -> addSubcategory($request));
    }

    public function edit ($category_id) {
        // TESTED....
        return Response::json($this -> model -> editCategory($category_id));
    }

    public function update (CategoriesRequest $request, $category_id) {
        // TESTED....
        return Response::json($this -> model -> updateCategory($request, $category_id));
    }

    public function delete ($category_id) {
        // TESTED....
        return Response::json($this -> model -> deleteCategory($category_id));
    }

    public function restore ($category_id) {
        // TESTED....
        return Response::json($this -> model -> restoreCategory($category_id));
    }

    public function subcategories ($category_id) {
        return Response::json($this -> model -> getSubcategories($category_id));
    }

    // DELETE SUBCATEGORIES
    public function deleteSubcategory ($subcategory_id) {
        return Response::json($this -> model -> deleteSubcategory($subcategory_id));
    }
}
