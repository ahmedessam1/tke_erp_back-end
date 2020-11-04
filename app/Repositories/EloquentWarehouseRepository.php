<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Repositories\Contracts\WarehouseRepository;
use App\Events\ActionHappened;
use App\Models\Warehouse;
use App\Traits\Data\GetWarehousesList;
use Auth;

class EloquentWarehouseRepository implements WarehouseRepository {
    use GetWarehousesList;

    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function getAllActiveWarehouses () {
        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $warehouses = $this->cache->remember('warehouses', function () {
            return json_encode(Warehouse::withCreatedByAndUpdatedBy() -> orderedName() -> paginate(30));
        });
        return json_decode($warehouses);
    }

    public function listingWarehouses () {
        return $this -> getWarehousesListOrderedByName();
    }

    public function getWarehousesSearchResult ($q) {
        $warehouses = Warehouse::withCreatedByAndUpdatedBy() -> orderedName()
            -> where('name', 'LIKE', '%'.$q.'%')
            -> orWhere('location', 'LIKE', '%'.$q.'%')
            -> orWhere('description', 'LIKE', '%'.$q.'%')
            -> paginate(30);
        return $warehouses;
    }

    public function addWarehouse($request) {
        $warehouse_fillable_values = array_merge(
            $request -> all(),
            ['created_by' => $this->getAuthUserId()]
        );
        $added_warehouse = Warehouse::create($warehouse_fillable_values);

        // STORE ACTION
        event(new ActionHappened('warehouse add', $added_warehouse, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forget('warehouses');
        return $added_warehouse;
    }

    public function editWarehouse($warehouse_id) {
        $edited_warehouse = Warehouse::withCreatedByAndUpdatedBy() -> find($warehouse_id);
        return $edited_warehouse;
    }

    public function updateWarehouse($request, $warehouse_id)
    {
        $warehouse = Warehouse::withCreatedByAndUpdatedBy() -> find($warehouse_id);
        $warehouse_fillable_values = array_merge(
            $request -> all(),
            ['updated_by'    => $this -> getAuthUserId()]
        );
        $warehouse -> update($warehouse_fillable_values);
        // STORE ACTION
        event(new ActionHappened('warehouse updated', $warehouse, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forget('warehouses');
        return $warehouse;
    }

    public function deleteWarehouse ($warehouse_id) {
        // DELETING THE WAREHOUSE
        $warehouse = Warehouse::find($warehouse_id);
        $warehouse -> delete();
        // STORE ACTION
        event(new ActionHappened('warehouse deleted', $warehouse, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forget('warehouses');
        return $warehouse;
    }

    public function restoreWarehouse ($warehouse_id) {
        // RESTORING THE WAREHOUSE
        $warehouse = Warehouse::withTrashed() -> find($warehouse_id);
        $warehouse -> restore();
        // STORE ACTION
        event(new ActionHappened('warehouse restored', $warehouse, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forget('warehouses');
        return $warehouse;
    }
}