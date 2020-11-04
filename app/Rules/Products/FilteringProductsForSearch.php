<?php

namespace App\Rules\Products;

use Illuminate\Contracts\Validation\Rule;

class FilteringProductsForSearch implements Rule
{
    public $filtering;

    public function __construct($filtering)
    {
        $this -> filtering = $filtering;
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
        $criterias = ['seasons', 'categories'];
        if(in_array($this -> filtering, $criterias)) return true;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.products_validation.filtering.wrong_filtering');
    }
}
