<?php

namespace App\Traits\Data;


use App\Models\Supplier\Supplier;

trait GetSuppliersList {
    public function getSuppliersListOrderedByName () {
        return Supplier::orderedName() -> pluck('name', 'id');
    }

    public function getSuppliersListOrderedByID () {
        return Supplier::orderedID() -> pluck('name', 'id');
    }
}