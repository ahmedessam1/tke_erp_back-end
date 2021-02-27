<?php

namespace App\Http\Requests\Refunds;

use Illuminate\Foundation\Http\FormRequest;

class RefundsRequest extends FormRequest
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
            'title' => 'required
                        |min:'.trans('validation_standards.names.min').'
                        |max:'.trans('validation_standards.names.max'),

            'assigned_user_id' => 'required|exists:landlord.users,id',

            'number' => 'required
                        |numeric',


            'model_id' => 'required',

            'date' => 'required|date',

            'type' => 'required|in:in,out'
        ];
    }

    public function messages()
    {
        return [
            // NUMBER
            'number.required' => trans('form_responses.refunds_validation.number.required'),

            // TITLE VALIDATION
            'title.required' => trans('form_responses.refunds_validation.title.required'),
            'title.min' => trans('form_responses.refunds_validation.title.min'),
            'title.max' => trans('form_responses.refunds_validation.title.max'),

            // EXPORT INVOICE
            'model_id.required' => trans('form_responses.refunds_validation.model_id.required'),

            // DATE
            'date.required' => trans('form_responses.refunds_validation.date.required'),
            'date.date' => trans('form_responses.refunds_validation.date.date'),

            // TYPE
            'type.required' => trans('form_responses.refunds_validation.type.required'),
            'type.in' => trans('form_responses.refunds_validation.type.in'),
        ];
    }

    protected function prepareForValidation()
    {
        // SETTING INVOICE_DATA DISCOUNT TO ZERO IF NULL AND INT
        $discount = $this->discount;
        $tax = $this->tax;
        if($discount == '' || $discount == null)
            $discount = 0;
        if($tax == '' || $tax == null)
            $tax = 0;
        $this->merge([
            'name'          => $this -> name,
            'number'        => $this -> number,
            'supplier_id'   => $this -> supplier_id,
            'tax'           => (int)$tax,
            'discount'      => (int)$discount,
            'date'          => $this -> date,
        ]);
    }
}
