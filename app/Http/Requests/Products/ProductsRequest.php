<?php

namespace App\Http\Requests\Products;

use App\Rules\Categories\SubcategoryMatchCategory;
use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
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
        $required = 'required|';
        if($this -> _method == 'PATCH')
            $required = '';

        return [
            'code'          => 'required|unique:products,code,'.$this -> product_id,

            'name'          => 'required
                                |min:'.trans('validation_standards.names.min').'
                                |max:'.trans('validation_standards.names.max').'
                                |unique:products,name,'.$this -> product_id,


            'barcode'       => 'nullable
                                |size:'.trans('validation_standards.barcode.size').'
                                |unique:products,barcode,'.$this -> product_id,

            'category_id'   => 'required
                                |exists:categories,id',

            'subcategories_id' => [ 'required', 'exists:subcategories,id', new SubcategoryMatchCategory($this -> _method, $this -> category_id) ],

            'image'         => $required.'image|mimes:'.trans('validation_standards.images.extensions').'
                                |max:'.trans('validation_standards.images.file_size'),

            'description'   => 'nullable
                                |min:'.trans('validation_standards.descriptions.min').'
                                |max:'.trans('validation_standards.descriptions.max'),
        ];
    }

    public function messages()
    {
        return [
            // CODE VALIDATION
            'code.required' => trans('form_responses.products_validation.code.required'),
            'code.unique'   => trans('form_responses.products_validation.code.unique'),

            // NAME VALIDATION
            'name.required' => trans('form_responses.products_validation.name.required'),
            'name.unique'   => trans('form_responses.products_validation.name.unique'),
            'name.max'      => trans('form_responses.products_validation.name.max'),
            'name.min'      => trans('form_responses.products_validation.name.min'),

            // BARCODE VALIDATION
            'barcode.size'    => trans('form_responses.products_validation.barcode.size'),
            'barcode.unique'  => trans('form_responses.products_validation.barcode.unique'),

            // CATEGORY ID
            'category_id.required'  => trans('form_responses.products_validation.category_id.required'),
            'category_id.exists'    => trans('form_responses.products_validation.category_id.exists'),

            // SUBCATEGORIES ID
            'subcategories_id.required'  => trans('form_responses.products_validation.subcategories_id.required'),
            'subcategories_id.exists'    => trans('form_responses.products_validation.subcategories_id.exists'),

            // IMAGE
            'image.required'    => trans('form_responses.products_validation.image.required'),
            'image.image'       => trans('form_responses.products_validation.image.mimes'),
            'image.mimes'       => trans('form_responses.products_validation.image.mimes'),
            'image.max'         => trans('form_responses.products_validation.image.max'),

            // DESCRIPTION
            'description.max' => trans('form_responses.products_validation.description.max'),
            'description.min' => trans('form_responses.products_validation.description.min'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'category_id' => (int)$this->category_id,
        ]);
    }
}
