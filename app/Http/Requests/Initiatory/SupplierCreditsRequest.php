<?php

namespace App\Http\Requests\Initiatory;

use Illuminate\Foundation\Http\FormRequest;

class SupplierCreditsRequest extends FormRequest
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
            'supplier_id'   => 'required|exists:suppliers,id',
            'amount'        => 'required|numeric|min:10|max:1000000',
            'date'          => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            // SUPPLIER CREDIT VALIDATION
            'supplier_id.required'  => trans('form_responses.supplier_initiatory_credit.supplier_id.required'),
            'supplier_id.exists'    => trans('form_responses.supplier_initiatory_credit.supplier_id.exists'),

            'amount.required'       => trans('form_responses.supplier_initiatory_credit.amount.required'),
            'amount.numeric'        => trans('form_responses.supplier_initiatory_credit.amount.numeric'),
            'amount.max'            => trans('form_responses.supplier_initiatory_credit.amount.numeric'),
            'amount.min'            => trans('form_responses.supplier_initiatory_credit.amount.numeric'),

            'date.required'         => trans('form_responses.supplier_initiatory_credit.date.required'),
            'date.date'             => trans('form_responses.supplier_initiatory_credit.date.date'),
        ];
    }
}
