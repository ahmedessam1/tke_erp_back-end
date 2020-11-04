<?php

namespace App\Traits\Data;

use App\Models\Position;

trait GetPositionsList {
    public function getPositionsListOrderedByName () {
        return Position::orderedName() -> pluck('name', 'id');
    }

    public function getPositionsListOrderedByID () {
        return Position::orderedID() -> pluck('name', 'id');
    }
}