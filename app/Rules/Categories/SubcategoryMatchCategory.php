<?php

namespace App\Rules\Categories;

use App\Models\Category\Subcategory;
use Illuminate\Contracts\Validation\Rule;

class SubcategoryMatchCategory implements Rule
{
    public $category_id, $method;

    public function __construct($method, $category_id)
    {
        $this -> method = $method;
        $this -> category_id = $category_id;
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
        if ($this -> method !== 'PATCH') {
            for ($i = 0; $i < count($value); $i++) {
                $flag = Subcategory::where('category_id', $this -> category_id) -> where('id', $value[$i]) -> exists();
                if (!$flag)
                    return false;
            }
            return true;
        } else return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.categories_validation.subcategories.category');
    }
}
