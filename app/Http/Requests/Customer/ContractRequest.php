<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|max:255',
            'discount' => 'required|numeric|max:80',
            'year' => [
                'required',
                Rule::unique('customer_contracts')->where(function ($query) {
                    return $query
                        ->where('customer_id', $this->customer_id)
                        ->where('discount', $this->discount)
                        ->where('year', $this->year);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => trans('form_responses.customers_validation.contracts.customer_id.required'),
            'customer_id.exists' => trans('form_responses.customers_validation.contracts.customer_id.exists'),

            'title.required' => trans('form_responses.customers_validation.contracts.title.required'),
            'title.max' => trans('form_responses.customers_validation.contracts.title.max'),

            'discount.required' => trans('form_responses.customers_validation.contracts.discount.required'),
            'discount.numeric' => trans('form_responses.customers_validation.contracts.discount.numeric'),
            'discount.max' => trans('form_responses.customers_validation.contracts.discount.max'),
            'discount.valid' => trans('form_responses.customers_validation.contracts.discount.valid'),

            'year.required' => trans('form_responses.customers_validation.contracts.year.required'),
            'year.unique' => trans('form_responses.customers_validation.contracts.year.unique'),
        ];
    }
}
