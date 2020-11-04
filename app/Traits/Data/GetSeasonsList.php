<?php

namespace App\Traits\Data;

use App\Models\Season;

trait GetSeasonsList {
    public function getSeasonsListOrderedByName () {
        return Season::orderedName() -> pluck('name', 'id');
    }

    public function getSeasonsListOrderedByID () {
        return Season::orderedID() -> pluck('name', 'id');
    }
}