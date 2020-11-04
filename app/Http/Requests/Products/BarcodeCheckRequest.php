<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class BarcodeCheckRequest extends FormRequest
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
            'barcode' => 'unique:products',
        ];
    }

    public function messages()
    {
        return [
            // BARCODE VALIDATION
            'barcode.unique' => trans('form_responses.products_validation.barcode.unique'),
        ];
    }
}
