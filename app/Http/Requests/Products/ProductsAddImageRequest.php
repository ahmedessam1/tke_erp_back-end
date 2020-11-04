<?php

namespace App\Http\Requests\Products;

use App\Rules\Products\ImageUploadMaxNumber;
use Illuminate\Foundation\Http\FormRequest;

class ProductsAddImageRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'file'       => [
                                'required','image','mimes:'.trans('validation_standards.images.extensions'),
                                'max:'.trans('validation_standards.images.file_size'),
                                new ImageUploadMaxNumber($this -> product_id)
                            ]
        ];
    }

    public function messages()
    {
        return [
            // PRODUCT ID VALIDATION
            'product_id.required'   => trans('form_responses.products_validation.product_id.required'),
            'product_id.exists'     => trans('form_responses.products_validation.product_id.exists'),

            // IMAGE
            'file.required'    => trans('form_responses.products_validation.image.required'),
            'file.image'       => trans('form_responses.products_validation.image.mimes'),
            'file.mimes'       => trans('form_responses.products_validation.image.mimes'),
            'file.max'         => trans('form_responses.products_validation.image.max'),
        ];
    }
}
