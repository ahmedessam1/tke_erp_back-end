<?php

namespace App\Rules\Products;

use App\Models\Product\ProductImages;
use Illuminate\Contracts\Validation\Rule;

class ImageUploadMaxNumber implements Rule
{
    public $product_id;

    public function __construct($product_id)
    {
        $this -> product_id = $product_id;
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
        $product_number_of_images = ProductImages::where('product_id', $this -> product_id) -> count();
        if($product_number_of_images >= trans('validation_standards.images.products.max_number'))
            return false;
        else
            return true;
    }

    public function message()
    {
        return trans('form_responses.products_validation.image.max_number');
    }
}
