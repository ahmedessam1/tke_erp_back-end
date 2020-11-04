<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehousesRequest extends FormRequest
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
            'name'          => 'required
                                |min:'.trans('validation_standards.names.min').'
                                |max:'.trans('validation_standards.names.max').'
                                |unique:warehouses,name,'.$this -> warehouse_id,

            'description'   => 'nullable
                                |min:'.trans('validation_standards.descriptions.min').'
                                |max:'.trans('validation_standards.descriptions.max'),

            'location'      => 'required
                                |min:'.trans('validation_standards.addresses.min').'
                                |max:'.trans('validation_standards.addresses.max'),
        ];
    }

    public function messages()
    {
        return [
            // NAME VALIDATION
            'name.required'         => trans('form_responses.warehouses_validation.name.required'),
            'name.unique'           => trans('form_responses.warehouses_validation.name.unique'),
            'name.max'              => trans('form_responses.warehouses_validation.name.max'),
            'name.min'              => trans('form_responses.warehouses_validation.name.min'),

            // DESCRIPTION VALIDATION
            'description.max'       => trans('form_responses.warehouses_validation.description.max'),
            'description.min'       => trans('form_responses.warehouses_validation.description.min'),

            // LOCATION VALIDATION
            'location.required'     => trans('form_responses.warehouses_validation.location.required'),
            'location.max'          => trans('form_responses.warehouses_validation.location.max'),
            'location.min'          => trans('form_responses.warehouses_validation.location.min'),
        ];
    }
}
