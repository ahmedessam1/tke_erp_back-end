<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoriesRequest extends FormRequest
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
            // SUBCATEGORIES
            'subcategories' => 'required',
            'subcategories.*.name' => 'required
                                        |min:'.trans('validation_standards.names.min').'
                                        |max:'.trans('validation_standards.names.max'),

            'subcategories.*.description' => 'nullable
                                               |min:'.trans('validation_standards.descriptions.min').'
                                               |max:'.trans('validation_standards.descriptions.max'),

        ];
    }


    public function messages()
    {
        return [
            // SUB-CATEGORIES
            // subcategories NAME VALIDATION
            'subcategories.*.name.required' => trans('form_responses.categories_validation.subcategories.name.required'),
            'subcategories.required'        => trans('form_responses.categories_validation.subcategories.name.required'),
            'subcategories.*.name.max'      => trans('form_responses.categories_validation.subcategories.name.max'),
            'subcategories.*.name.min'      => trans('form_responses.categories_validation.subcategories.name.min'),

            // subcategories DESCRIPTION VALIDATION
            'subcategories.*.description.max' => trans('form_responses.categories_validation.subcategories.description.max'),
            'subcategories.*.description.min' => trans('form_responses.categories_validation.subcategories.description.min'),
        ];
    }
}
