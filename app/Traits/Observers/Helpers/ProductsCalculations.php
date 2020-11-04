<?php

namespace App\Traits\Observers\Helpers;

use App\Models\Product\ProductLog;

trait ProductsCalculations {
    /**
     * @param $operator
     * @param $products
     */
    public function updateProductActualQuantity ($operator, $product) {
        $product_log = ProductLog::where('product_id', $product->product_id)->first();
        $product_actual_quantity = $product_log->available_quantity;
        if ($operator === 'adding')
            $product_log->available_quantity = $product_actual_quantity + $product->quantity;
        else if ($operator === 'subtracting')
            $product_log->available_quantity = $product_actual_quantity - $product->quantity;
        $product_log->save();
    }

    /**
     * @param $product
     */
    public function updateProductAveragePurchasePrice ($product) {
        $product_log = ProductLog::where('product_id', $product->product_id)->first();

        // MULTIPLY PRODUCT ACTUAL QUANTITY AND ACTUAL AVERAGE PURCHASE PRICE
        $product_actual_quantity = $product_log->available_quantity;
        $product_actual_average_purchase_price = $product_log->average_purchase_price;
        $multiply_actual_quantity_and_average_purchase_price = $product_actual_quantity * $product_actual_average_purchase_price;

        // MULTIPLY NEW PRODUCT QUANTITY AND PURCHASE PRICE
        $new_quantity = $product->quantity;
        $new_purchase_price = $product->item_net_price;
        $multiply_new_quantity_and_purchase_price = $new_quantity * $new_purchase_price;

        $calculated_new_average_purchase_price = ($multiply_actual_quantity_and_average_purchase_price + $multiply_new_quantity_and_purchase_price)
            /
            ($product_actual_quantity + $new_quantity);

        // SAVING THE NEW AVERAGE PURCHASE PRICE
        $product_log->average_purchase_price = $calculated_new_average_purchase_price;
        $product_log->save();
    }

    /**
     * @param $product
     */
    public function updateProductAverageSellPrice ($product) {
        $product_log = ProductLog::where('product_id', $product->product_id)->first();

        // MULTIPLY PRODUCT ACTUAL QUANTITY AND ACTUAL AVERAGE SELLING PRICE
        $product_actual_quantity = $product_log->available_quantity;
        $product_actual_average_sell_price = $product_log->average_sell_price;
        $multiply_actual_quantity_and_average_sell_price = $product_actual_quantity * $product_actual_average_sell_price;

        // MULTIPLY NEW PRODUCT QUANTITY AND SELL PRICE
        $new_quantity = $product->quantity;
        $new_purchase_price = $product->item_net_price;
        $multiply_new_quantity_and_sell_price = $new_quantity * $new_purchase_price;

        $calculated_new_average_sell_price = ($multiply_actual_quantity_and_average_sell_price + $multiply_new_quantity_and_sell_price)
            /
            ($product_actual_quantity + $new_quantity);

        // SAVING THE NEW AVERAGE PURCHASE PRICE
        $product_log->average_sell_price = $calculated_new_average_sell_price;
        $product_log->save();
    }
}
