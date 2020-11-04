<?php

namespace App\Repositories\Contracts;

interface RefundRepository
{
    // RETURN REFUNDS
    public function index($request);

    // REFUND SEARCH
    public function search($request);

    // REFUND SHOW
    public function show($refund_id);

    // REFUND STORE
    public function storeRefundOrder($request);

    public function storeRefundOrderProducts($request);

    // REFUND EDIT
    public function edit($refund_id);

    public function update($request, $refund_id);

    // REMOVE PRODUCT FROM REFUND ORDER
    public function removeProductFromRefundOrder($refund_id, $product_id);

    // REFUND APPROVE
    public function approve($refund_id);

    // REFUND DELETE
    public function delete($refund_id);
}
