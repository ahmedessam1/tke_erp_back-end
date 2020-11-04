<?php

namespace App\Repositories\Contracts;

interface WarehouseRepository {
    // RETURN ALL THE ACTIVE WAREHOUSES ONLY
    public function getAllActiveWarehouses();

    // WAREHOUSES LISTING
    public function listingWarehouses();

    // SEARCH WAREHOUSES
    public function getWarehousesSearchResult($q);

    // ADD NEW WAREHOUSE
    public function addWarehouse($request);

    // EDIT WAREHOUSE
    public function editWarehouse($warehouse_id);

    // UPDATE WAREHOUSE
    public function updateWarehouse($request, $warehouse_id);

    // DELETE WAREHOUSE
    public function deleteWarehouse($warehouse_id);

    // RESTORE WAREHOUSE
    public function restoreWarehouse($warehouse_id);

}