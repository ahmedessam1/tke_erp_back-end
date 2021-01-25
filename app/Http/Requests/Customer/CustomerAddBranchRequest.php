<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAddBranchRequest extends FormRequest
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
            // BRANCHES
            // ADDRESS
            'branches.*.address'  => 'required
                                    |min:'.trans('validation_standards.addresses.min').'
                                    |max:'.trans('validation_standards.addresses.max'),

            // CONTACTS
            'branches.*.contacts'                            => 'required',
            'branches.*.contacts.*.name'          => 'required
                                                                    |min:'.trans('validation_standards.names.min').'
                                                                    |max:'.trans('validation_standards.names.max'),
            'branches.*.contacts.*.phone_number'  => 'required
                                                                    |min:'.trans('validation_standards.phone_numbers.min').'
                                                                    |max:'.trans('validation_standards.phone_numbers.max'),
            'branches.*.contacts.*.position_id'   => 'required
                                                                    |exists:positions,id'
        ];
    }

    public function messages()
    {
        return [
            // BRANCHES
            // ADDRESS VALIDATION
            'branches.*.address.required' => trans('form_responses.customers_validation.branches.address.required'),
            'branches.*.address.max'      => trans('form_responses.customers_validation.branches.address.max'),
            'branches.*.address.min'      => trans('form_responses.customers_validation.branches.address.min'),

            // CONTACTS
            'branches.*.contacts'    => trans('form_responses.customers_validation.address_contact.address.required'),
            // CONTACT NAME
            'branches.*.contacts.*.name.required' => trans('form_responses.customers_validation.branches.contacts.name.required'),
            'branches.*.contacts.*.name.min' => trans('form_responses.customers_validation.branches.contacts.name.min'),
            'branches.*.contacts.*.name.max' => trans('form_responses.customers_validation.branches.contacts.name.max'),

            // CONTACT PHONE NUMBER
            'branches.*.contacts.*.phone_number.required' => trans('form_responses.customers_validation.branches.contacts.phone_number.required'),
            'branches.*.contacts.*.phone_number.min' => trans('form_responses.customers_validation.branches.contacts.phone_number.min'),
            'branches.*.contacts.*.phone_number.max' => trans('form_responses.customers_validation.branches.contacts.phone_number.max'),

            // CONTACT POSITION
            'branches.*.contacts.*.position_id.required' => trans('form_responses.customers_validation.branches.contacts.position_id.required'),
            'branches.*.contacts.*.position_id.exists' => trans('form_responses.customers_validation.branches.contacts.position_id.exists'),
        ];
    }

    protected function prepareForValidation()
    {
        // SETTING BRANCH DISCOUNT TO ZERO IF NULL AND INT
        $holder = [];
        foreach($this -> branches as $branch) {
            array_push($holder, [
                'address'    => $branch['address'],
                'notes'      => $branch['notes'],
                'contacts'   => $branch['contacts'],
                'sellers_id' => $branch['sellers_id'],
            ]);
        }
        $this -> merge([
            "branches" => $holder
        ]);
    }
}
