<?php

namespace App\Http\Controllers;

use App\Http\Requests\Refunds\RefundedProductsRequest;
use App\Http\Requests\Refunds\RefundsRequest;
use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\RefundRepository;
use Illuminate\Http\Request;
use Response;

class RefundsController extends Controller
{
    protected $model;

    public function __construct(RefundRepository $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        return Response::json($this->model->index($request->all()));
    }

    public function search(TableSearchRequest $request)
    {
        return Response::json($this->model->search($request->all()));
    }

    public function show($refund_id)
    {
        return Response::json($this->model->show($refund_id));
    }

    public function storeRefundOrder(RefundsRequest $request)
    {
        return Response::json($this->model->storeRefundOrder($request));
    }

    public function storeRefundOrderProducts(RefundedProductsRequest $request)
    {
        return Response::json($this->model->storeRefundOrderProducts($request));
    }

    public function edit($refund_id)
    {
        return Response::json($this->model->edit($refund_id));
    }

    public function update(RefundsRequest $request, $refund_id)
    {
        return Response::json($this->model->update($request, $refund_id));
    }

    public function removeProductFromRefundOrder($refund_id, $product_id)
    {
        return Response::json($this->model->removeProductFromRefundOrder($refund_id, $product_id));
    }

    public function approve(Request $request, $refund_id)
    {
        return Response::json($this->model->approve($refund_id));
    }

    public function delete($refund_id)
    {
        return Response::json($this->model->delete($refund_id));
    }
}
