<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;

class SuppliersPaymentRequest extends FormRequest
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
            'date'            => 'required|date',
            'supplier_id'     => 'required|exists:suppliers,id',
            'supplier_address_id' => 'required|exists:supplier_addresses,id',
            'supplier_contact_id' => 'required|exists:supplier_address_contacts,id',
            'amount'          => 'required
                                |numeric
                                |max:'.trans('validation_standards.payment.max').'
                                |min:'.trans('validation_standards.payment.min').'',
            'payment_type_id' => 'required|exists:payment_types,id',
        ];
    }

    public function messages()
    {
        return [
            // DATE
            'date.required' => trans('form_responses.suppliers_validation.payments.date.required'),
            'date.date'     => trans('form_responses.suppliers_validation.payments.date.required'),

            // SUPPLIERS ID VALIDATION
            'supplier_id.required' => trans('form_responses.suppliers_validation.payments.supplier.required'),
            'supplier_id.exists' => trans('form_responses.suppliers_validation.payments.supplier.exists'),

            // SUPPLIERS ADDRESSES ID VALIDATION
            'supplier_address_id.required' => trans('form_responses.suppliers_validation.payments.supplier_address_id.required'),
            'supplier_address_id.exists' => trans('form_responses.suppliers_validation.payments.supplier_address_id.exists'),

            // SUPPLIERS CONTACTS ID VALIDATION
            'supplier_contact_id.required' => trans('form_responses.suppliers_validation.payments.supplier_contact_id.required'),
            'supplier_contact_id.exists' => trans('form_responses.suppliers_validation.payments.supplier_contact_id.exists'),

            // AMOUNT VALIDATION
            'amount.required'   => trans('form_responses.suppliers_validation.payments.amount.required'),
            'amount.numeric'    => trans('form_responses.suppliers_validation.payments.amount.numeric'),
            'amount.max'        => trans('form_responses.suppliers_validation.payments.amount.max'),
            'amount.min'        => trans('form_responses.suppliers_validation.payments.amount.min'),

            // PAYMENT TYPE VALIDATION
            'payment_type_id.required' => trans('form_responses.suppliers_validation.payments.payment_types.required'),
            'payment_type_id.exists' => trans('form_responses.suppliers_validation.payments.payment_types.exists'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => (float)$this->amount,
        ]);
    }
}
