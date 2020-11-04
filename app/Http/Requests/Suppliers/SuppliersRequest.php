<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;

class SuppliersRequest extends FormRequest
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
            'name'                                              => 'required
                                                                    |max:'.trans('validation_standards.names.max').'
                                                                    |min:'.trans('validation_standards.names.min').'
                                                                    |unique:suppliers,name,'.$this -> supplier_id,
            'description'                                       => 'nullable|
                                                                    |min: '.trans('validation_standards.descriptions.min').'
                                                                    |max:'.trans('validation_standards.descriptions.max'),

            'address_contact_inputs'                            => 'required',
            'address_contact_inputs.*.address'                  => 'required
                                                                    |min:'.trans('validation_standards.addresses.min').'
                                                                    |max:'.trans('validation_standards.addresses.max'),
            'address_contact_inputs.*.contacts.*.name'          => 'required
                                                                    |min:'.trans('validation_standards.names.min').'
                                                                    |max:'.trans('validation_standards.names.max'),
            'address_contact_inputs.*.contacts.*.phone_number'  => 'required
                                                                    |min:'.trans('validation_standards.phone_numbers.min').'
                                                                    |max:'.trans('validation_standards.phone_numbers.max'),
            'address_contact_inputs.*.contacts.*.position_id'   => 'required
                                                                    |exists:positions,id'
        ];
    }

    public function messages()
    {
        return [
             // NAME
            'name.required'             => trans('form_responses.suppliers_validation.name.required'),
            'name.unique'               => trans('form_responses.suppliers_validation.name.unique'),
            'name.min'                  => trans('form_responses.suppliers_validation.name.min'),
            'name.max'                  => trans('form_responses.suppliers_validation.name.max'),

            // DESCRIPTION
            'description.min'           => trans('form_responses.suppliers_validation.description.min'),
            'description.max'           => trans('form_responses.suppliers_validation.description.max'),

            'address_contact_inputs'    => trans('form_responses.suppliers_validation.address_contact.address.required'),
            // ADDRESS
            'address_contact_inputs.*.address.required' => trans('form_responses.suppliers_validation.address_contact_inputs.address.required'),
            'address_contact_inputs.*.address.min'      => trans('form_responses.suppliers_validation.address_contact_inputs.address.min'),
            'address_contact_inputs.*.address.max'      => trans('form_responses.suppliers_validation.address_contact_inputs.address.max'),

            // CONTACT NAME
            'address_contact_inputs.*.contacts.*.name.required' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.name.required'),
            'address_contact_inputs.*.contacts.*.name.min' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.name.min'),
            'address_contact_inputs.*.contacts.*.name.max' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.name.max'),

            // CONTACT PHONE NUMBER
            'address_contact_inputs.*.contacts.*.phone_number.required' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.phone_number.required'),
            'address_contact_inputs.*.contacts.*.phone_number.min' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.phone_number.min'),
            'address_contact_inputs.*.contacts.*.phone_number.max' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.phone_number.max'),

            // CONTACT POSITION
            'address_contact_inputs.*.contacts.*.position_id.required' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.position_id.required'),
            'address_contact_inputs.*.contacts.*.position_id.exists' => trans('form_responses.suppliers_validation.address_contact_inputs.contacts.position_id.exists'),
        ];
    }
}
