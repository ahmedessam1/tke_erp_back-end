<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesRequest extends FormRequest
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
            'name'          => 'required
                                |min:'.trans('validation_standards.names.min').'
                                |max:'.trans('validation_standards.names.max').'
                                |unique:categories,name,'.$this -> category_id,

            'description'   => 'nullable
                                |min:'.trans('validation_standards.descriptions.min').'
                                |max:'.trans('validation_standards.descriptions.max'),

        ];
    }

    public function messages()
    {
        return [
            // NAME VALIDATION
            'name.required'         => trans('form_responses.categories_validation.name.required'),
            'name.unique'           => trans('form_responses.categories_validation.name.unique'),
            'name.max'              => trans('form_responses.categories_validation.name.max'),
            'name.min'              => trans('form_responses.categories_validation.name.min'),

            // DESCRIPTION VALIDATION
            'description.max'       => trans('form_responses.categories_validation.description.max'),
            'description.min'       => trans('form_responses.categories_validation.description.min'),
        ];
    }
}
