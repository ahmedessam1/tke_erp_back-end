<?php

namespace App\Traits\Data;

use App\Models\Category\Category;

trait GetCategoriesList {
    public function getCategoriesListOrderedByName () {
        return Category::orderedName() -> pluck('name', 'id');
    }

    public function getCategoriesListOrderedByID () {
        return Category::orderedID() -> pluck('name', 'id');
    }
}