<?php

namespace App\Traits\Data;

use App\Models\Warehouse;

trait GetWarehousesList {
    public function getWarehousesListOrderedByName () {
        return Warehouse::orderedName() -> pluck('name', 'id');
    }

    public function getWarehousesListOrderedByID () {
        return Warehouse::orderedID() -> pluck('name', 'id');
    }
}