<?php

namespace App\Http\Controllers;

use App\Http\Requests\Initiatory\CustomerBranchCreditsRequest;
use App\Http\Requests\Initiatory\ProductCreditsRequest;
use App\Http\Requests\Initiatory\SupplierCreditsRequest;
use App\Http\Requests\TableSearchRequest;
use App\Models\Product\ProductCredits;
use App\Repositories\Contracts\InitiatoryRepository;
use Response;

class InitiatoryController extends Controller
{
    protected $model;
    public function __construct(InitiatoryRepository $seasons) {
        $this->model = $seasons;
    }

    // PRODUCT CREDITS
    public function productCreditsIndex () {
        return Response::json($this->model->productCreditsIndex());
    }

    public function productCreditsSearch (TableSearchRequest $request) {
        return Response::json($this->model->productCreditsSearch($request->input('query')));
    }

    public function productCreditsStore (ProductCreditsRequest $request) {
        return Response::json($this->model->productCreditsStore($request));
    }

    public function productCreditsDelete ($product_credit_id) {
        return Response::json($this->model->productCreditsDelete($product_credit_id));
    }



    // SUPPLIER CREDITS
    public function supplierCreditsIndex () {
        return Response::json($this->model->supplierCreditsIndex());
    }

    public function supplierCreditsSearch (TableSearchRequest $request) {
        return Response::json($this->model->supplierCreditsSearch($request->input('query')));
    }

    public function supplierCreditsStore (SupplierCreditsRequest $request) {
        return Response::json($this->model->supplierCreditsStore($request));
    }

    public function supplierCreditsDelete ($supplier_credit_id) {
        return Response::json($this->model->supplierCreditsDelete($supplier_credit_id));
    }


    // CUSTOMER CREDITS
    public function customerCreditsIndex () {
        return Response::json($this->model->customerCreditsIndex());
    }

    public function customerCreditsSearch (TableSearchRequest $request) {
        return Response::json($this->model->customerCreditsSearch($request->input('query')));
    }

    public function customerCreditsStore (CustomerBranchCreditsRequest $request) {
        return Response::json($this->model->customerCreditsStore($request));
    }

    public function customerCreditsDelete ($customer_credit_id) {
        return Response::json($this->model->customerCreditsDelete($customer_credit_id));
    }
}
