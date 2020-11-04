<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableSearchRequest;
use App\Models\Warehouse;
use App\Repositories\Contracts\WarehouseRepository;
use App\Http\Requests\WarehousesRequest;
use Response;

class WarehousesController extends Controller
{
    protected $model;
    public function __construct(WarehouseRepository $warehouses) {
        $this -> model = $warehouses;
    }

    public function index () {
        // TESTED....
        $getWarehouses = $this -> model -> getAllActiveWarehouses();
        return Response::json($getWarehouses);
    }

    public function listing () {
        return Response::json($this -> model -> listingWarehouses());
    }

    public function search (TableSearchRequest $request) {
        // TESTED....
        $getWarehouses = $this -> model -> getWarehousesSearchResult($request -> input('query'));
        return Response::json($getWarehouses);
    }

    public function store (WarehousesRequest $request) {
        // TESTED....
        $added_warehouse = $this -> model -> addWarehouse($request);
        return Response::json($added_warehouse);
    }

    public function edit ($warehouse_id) {
        // TESTED....
        $edited_warehouse = $this -> model -> editWarehouse($warehouse_id);
        return Response::json($edited_warehouse);
    }

    public function update (WarehousesRequest $request, $warehouse_id) {
        // TESTED....
        $updated_warehouse = $this -> model -> updateWarehouse($request, $warehouse_id);
        return Response::json($updated_warehouse);
    }

    public function delete ($warehouse_id) {
        // TESTED....
        $delete_warehouse = $this -> model -> deleteWarehouse($warehouse_id);
        return Response::json($delete_warehouse);
    }

    public function restore ($warehouse_id) {
        // TESTED....
        $restore_warehouse = $this -> model -> restoreWarehouse($warehouse_id);
        return Response::json($restore_warehouse);
    }
}
