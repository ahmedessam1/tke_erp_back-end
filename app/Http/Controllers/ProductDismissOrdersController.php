<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\ProductDismissOrderRepository;
use App\Http\Requests\ProductDismissOrderRequest;
use Illuminate\Http\Request;
use Response;

class ProductDismissOrdersController extends Controller
{
    protected $model;
    public function __construct(ProductDismissOrderRepository $model) {
        $this->model = $model;
    }

    public function index (Request $request) {
        return Response::json($this->model->index($request->all()));
    }

    public function search (TableSearchRequest $request) {
        return Response::json($this->model->search($request->all()));
    }

    public function show ($product_dismiss_order_id) {
        return Response::json($this->model->show($product_dismiss_order_id));
    }

    public function store (ProductDismissOrderRequest $request) {
        return Response::json($this->model->store($request));
    }

    public function delete ($product_dismiss_order_id) {
        return Response::json($this->model->delete($product_dismiss_order_id));
    }

    public function approve ($product_dismiss_order_id) {
        return Response::json($this->model->approve($product_dismiss_order_id));
    }
}
