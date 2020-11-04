<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeasonsRequest extends FormRequest
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
                                |unique:seasons,name,'.$this -> season_id,
            'starting_date' => 'required|date',
            'ending_date'   => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            // NAME VALIDATION
            'name.required' => trans('form_responses.seasons_validation.name.required'),
            'name.unique'   => trans('form_responses.seasons_validation.name.unique'),
            'name.max'      => trans('form_responses.seasons_validation.name.max'),
            'name.min'      => trans('form_responses.seasons_validation.name.min'),

            // STARTING DATE
            'starting_date.required'    => trans('form_responses.seasons_validation.starting_date.required'),
            'starting_date.date'        => trans('form_responses.seasons_validation.starting_date.date'),

            // ENDING DATE
            'ending_date.required'      => trans('form_responses.seasons_validation.ending_date.required'),
            'ending_date.date'          => trans('form_responses.seasons_validation.ending_date.date'),
        ];
    }
}
