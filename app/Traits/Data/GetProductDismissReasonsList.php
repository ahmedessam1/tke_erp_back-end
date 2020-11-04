<?php

namespace App\Traits\Data;


use App\Models\ProductDismissReasons;

trait GetProductDismissReasonsList {
    public function getProductDismissReasonsListOrderedByReason () {
        return ProductDismissReasons::orderBy('reason', 'ASC') -> pluck('reason', 'id');
    }

    public function getProductDismissReasonsListOrderedByID () {
        return Category::orderedID() -> pluck('name', 'id');
    }
}