<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
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
            // CUSTOMER INFO VALIDATION
            'name'   => 'required|unique:customers,name,'.$this -> customer_id.'
                        |min:'.trans('validation_standards.names.min').'
                        |max:'.trans('validation_standards.names.max'),

        ];
    }

    public function messages() {
        return [
            // CUSTOMER VALIDATION
            // NAME VALIDATION
            'name.required' => trans('form_responses.customers_validation.name.required'),
            'name.unique' => trans('form_responses.customers_validation.name.unique'),
            'name.max' => trans('form_responses.customers_validation.name.max'),
            'name.min' => trans('form_responses.customers_validation.name.min'),

        ];
    }
}
