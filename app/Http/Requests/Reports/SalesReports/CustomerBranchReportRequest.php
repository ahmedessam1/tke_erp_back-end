<?php

namespace App\Http\Requests\Reports\SalesReports;

use Illuminate\Foundation\Http\FormRequest;

class CustomerBranchReportRequest extends FormRequest
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
            'customers_id'   => 'required',
            'year' => 'required
                    |integer
                    |min:'.(int)trans('validation_standards.year_report.min').'
                    |max:'.(int)trans('validation_standards.year_report.max'),
        ];
    }

    public function messages()
    {
        return [
            'customers_id.required' => trans('form_responses.customer_branch_initiatory_credit.customer_branch_id.required'),

            'year.required' => trans('form_responses.customer_branch_initiatory_credit.date.required'),
            'year.integer'  => trans('form_responses.customer_branch_initiatory_credit.date.required'),
            'year.min'      => trans('form_responses.customer_branch_initiatory_credit.date.date'),
            'year.max'      => trans('form_responses.customer_branch_initiatory_credit.date.date'),
        ];
    }
}
