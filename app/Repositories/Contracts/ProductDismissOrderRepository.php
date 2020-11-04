<?php

namespace App\Repositories\Contracts;

interface ProductDismissOrderRepository {
    // RETURN ALL PRODUCT DISMISS ORDERS
    public function index($request);

    // SEARCH PRODUCT DISMISS ORDERS
    public function search($request);

    // SHOW PRODUCT DISMISS ORDER DETAILS
    public function show($product_dismiss_order_id);

    // ADD NEW PRODUCT DISMISS ORDER
    public function store($request);

    // DELETE PRODUCT DISMISS ORDER
    public function delete($product_dismiss_order_id);

    // APPROVE PRODUCT DISMISS ORDER
    public function approve($product_dismiss_order_id);
}