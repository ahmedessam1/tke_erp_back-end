<?php

namespace App\Http\Requests\Custodies;

use Illuminate\Foundation\Http\FormRequest;

class MoneyStoreRequest extends FormRequest
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

    public function rules()
    {
        return [
            'title' => 'required|max:'.trans('validation_standards.titles.max'),
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'note' => 'max:'.trans('validation_standards.descriptions.max'),
        ];
    }

    public function messages()
    {
        return [
            // TITLE VALIDATION
            'title.required' => trans('form_responses.expenses.title.required'),
            'title.max' => trans('form_responses.expenses.title.max'),
            'title.min' => trans('form_responses.expenses.title.min'),

            // AMOUNT VALIDATION
            'amount.required' => trans('form_responses.expenses.amount.required'),
            'amount.numeric' => trans('form_responses.expenses.amount.numeric'),

            // DATE VALIDATION
            'date.required' => trans('form_responses.expenses.date.required'),
            'date.date' => trans('form_responses.expenses.date.date'),

            // USER ID VALIDATION
            'user_id.exists' => trans('form_responses.expenses.user_id.exists'),

            // PAYMENT TYPE
            'payment_type_id.required' => trans('form_responses.expenses.payment_type_id.required'),
            'payment_type_id.exists' => trans('form_responses.expenses.payment_type_id.exists'),
        ];
    }
}
