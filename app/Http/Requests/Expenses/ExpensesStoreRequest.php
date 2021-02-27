<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class ExpensesStoreRequest extends FormRequest
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
            'expense_type_id' => 'required|exists:expenses_types,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:landlord.users,id',
            'note' => 'max:'.trans('validation_standards.descriptions.max'),
            'payment_type_id' => 'required|exists:payment_types,id',
        ];
    }

    public function messages()
    {
        return [
            // TITLE VALIDATION
            'title.required' => trans('form_responses.expenses.title.required'),
            'title.max' => trans('form_responses.expenses.title.max'),
            'title.min' => trans('form_responses.expenses.title.min'),

            // EXPENSES TYPE ID VALIDATION
            'expense_type_id.required' => trans('form_responses.expenses.expense_type_id.required'),

            // AMOUNT VALIDATION
            'amount.required' => trans('form_responses.expenses.amount.required'),
            'amount.numeric' => trans('form_responses.expenses.amount.numeric'),

            // DATE VALIDATION
            'date.required' => trans('form_responses.expenses.date.required'),
            'date.date' => trans('form_responses.expenses.date.date'),

            // CUSTOMER ID VALIDATION
            'customer_id.exists' => trans('form_responses.expenses.customer_id.exists'),

            // USER ID VALIDATION
            'user_id.exists' => trans('form_responses.expenses.user_id.exists'),

            // PAYMENT TYPE
            'payment_type_id.required' => trans('form_responses.expenses.payment_type_id.required'),
            'payment_type_id.exists' => trans('form_responses.expenses.payment_type_id.exists'),
        ];
    }
}
