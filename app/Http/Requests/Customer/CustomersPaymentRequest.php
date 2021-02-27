<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomersPaymentRequest extends FormRequest
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
            'date'                  => 'required|date',
            'customer_id'    => 'required|exists:customers,id',
            'amount'          => 'required
                                |numeric
                                |max:'.trans('validation_standards.payment.max').'
                                |min:'.trans('validation_standards.payment.min').'',
            'money_courier_id'  => 'required|exists:landlord.users,id',
            'payment_type_id'   => 'required|exists:payment_types,id',
        ];
    }

    public function messages()
    {
        return [
            // DATE
            'date.required' => trans('form_responses.suppliers_validation.payments.date.required'),
            'date.date'     => trans('form_responses.suppliers_validation.payments.date.required'),

            // CUSTOMERS ID VALIDATION
            'customer_id.required' => trans('form_responses.customers_validation.payments.customer.required'),
            'customer_id.exists' => trans('form_responses.customers_validation.payments.customer.exists'),

            // CUSTOMERS ID VALIDATION
            'money_courier_id.required' => trans('form_responses.customers_validation.payments.money_courier.required'),
            'money_courier_id.exists' => trans('form_responses.customers_validation.payments.money_courier.exists'),

            // AMOUNT VALIDATION
            'amount.required'   => trans('form_responses.customers_validation.payments.amount.required'),
            'amount.numeric'    => trans('form_responses.customers_validation.payments.amount.numeric'),
            'amount.max'        => trans('form_responses.customers_validation.payments.amount.max'),
            'amount.min'        => trans('form_responses.customers_validation.payments.amount.min'),

            // PAYMENT TYPE VALIDATION
            'payment_type_id.required' => trans('form_responses.customers_validation.payments.payment_types.required'),
            'payment_type_id.exists' => trans('form_responses.customers_validation.payments.payment_types.exists'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => (float)$this->amount,
        ]);
    }
}
