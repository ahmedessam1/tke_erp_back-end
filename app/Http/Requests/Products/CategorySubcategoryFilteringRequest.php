<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CategorySubcategoryFilteringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id'   => 'nullable|exists:categories,id',
            'q'             => 'nullable',
            'subcategory_id'=> 'nullable|exists:subcategories,id'
        ];
    }

    public function messages()
    {
        return [
            // CATEGORY ID VALIDATION
            'category_id.exists' => trans('form_responses.products_validation.category_id.exists'),

            // SUBCATEGORY ID VALIDATION
            'subcategory_id.exists'     => trans('form_responses.products_validation.subcategories_id.exists'),
        ];
    }
}
