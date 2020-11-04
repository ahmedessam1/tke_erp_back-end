<?php

namespace App\Traits\Data;

use App\Models\Product\Product;

trait FilterProducts {
    protected function categorySubcategoryProductsFiltering ($category_id, $subcategories_id, $q, $type) {
        $condition_queries = Product::search($q);

        if ($category_id != '')
            $condition_queries->where('category_id', $category_id);

        if ($type === 'selling') {
            $collection = $condition_queries
                ->take(30)
                ->get();
            $collection->load('category', 'subcategories', 'images');
            $filtered_collection = $collection->filter(function ($item) {
                return $item->report_total_quantity > 0;
            })->values();

            return $filtered_collection;
        } else {
            $collection = $condition_queries
               ->take(30)
               ->get();
            $collection->load('category', 'subcategories', 'images');

            return $collection;
        }
    }
}
