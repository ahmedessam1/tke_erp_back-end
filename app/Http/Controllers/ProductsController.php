<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\BarcodeCheckRequest;
use App\Http\Requests\Products\CategorySubcategoryFilteringRequest;
use App\Http\Requests\Products\ProductsAddImageRequest;
use App\Http\Requests\Products\ProductsFilteringRequest;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\ProductRepository;
use Response;

class ProductsController extends Controller
{
    protected $model;

    public function __construct(ProductRepository $products)
    {
        $this->model = $products;
    }

    public function index()
    {
        // TESTED....
        $getProducts = $this->model->getAllActiveProducts();
        return Response::json($getProducts);
    }

    public function show($product_id)
    {
        // TESTED....
        return Response::json($this->model->showProductDetails($product_id));
    }

    public function search(TableSearchRequest $request)
    {
        // TESTED....
        return Response::json($this->model->getProductsSearchResult($request->input('query'), $request->input('category_id')));
    }

    public function categorySubcategoryFiltering(CategorySubcategoryFilteringRequest $filtering)
    {
        return Response::json($this->model->categorySubcategoryFiltering($filtering));
    }

    public function add()
    {
        // TESTED....
        return Response::json($this->model->getProductRequirements());
    }

    public function store(ProductsRequest $request)
    {
        // TESTED....
        return Response::json($this->model->storeProduct($request));
    }

    public function addImage(ProductsAddImageRequest $request)
    {
        // TESTED....
        return Response::json($this->model->storeNewImageToExistingProduct($request));
    }

    public function removeImage($product_id, $image_id)
    {
        // TESTED....
        return Response::json($this->model->removeImageFromExistingProduct($product_id, $image_id));
    }

    public function edit($product_id)
    {
        // TESTED....
        return Response::json($this->model->editProduct($product_id));
    }

    public function update(ProductsRequest $request, $product_id)
    {
        // TESTED....
        return Response::json($this->model->updateProduct($request, $product_id));
    }

    public function delete($product)
    {
        // TESTED....
        return Response::json($this->model->deleteProduct($product));
    }

    public function restore($product_id)
    {
        // TESTED....
        return Response::json($this->model->restoreProduct($product_id));
    }

    public function barcodeCheck(BarcodeCheckRequest $request)
    {
        return;
    }
}
