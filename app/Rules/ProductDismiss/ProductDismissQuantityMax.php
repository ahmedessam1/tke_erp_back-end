<?php

namespace App\Rules\ProductDismiss;

use App\Models\Product\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductDismissQuantityMax implements Rule
{
    private $products;

    public function __construct($products)
    {
        $this -> products = $products;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // IF PRODUCT IS DUPLICATED THEN SUM THE PRODUCT QUANTITIES
        $counter = count($this -> products);

        $new_products_array = [];

        // SUM ALL QUANTITIES OF THE SAME PRODUCT
        for ($x = 0; $x < $counter; $x++) {
            if (!array_key_exists($this -> products[$x]['product_id'], $new_products_array))
                $new_products_array[$this -> products[$x]['product_id']] = $this -> products[$x]['quantity'];
            else
                $new_products_array[$this -> products[$x]['product_id']] += $this -> products[$x]['quantity'];
        }

        foreach ($new_products_array as $key => $value) {
            $product = Product::find($key);
            if ($product) {
                $product_quantity = $product -> report_total_quantity;
                $product_dismiss_quantity = $value;
                if ($product_dismiss_quantity > $product_quantity)
                    return false;
            } else return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.product_dismiss_order_validation.products.quantity.max');
    }
}
