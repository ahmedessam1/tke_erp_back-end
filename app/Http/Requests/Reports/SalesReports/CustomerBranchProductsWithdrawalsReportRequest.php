<?php

namespace App\Http\Requests\Reports\SalesReports;

use App\Rules\ToDateMoreThanFromDateRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerBranchProductsWithdrawalsReportRequest extends FormRequest
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
            'customer_branch_id' => 'required|exists:customer_branches,id',
            'from_date' => 'required|date',
            'to_date' => ['required', 'date', new ToDateMoreThanFromDateRule($this -> from_date, $this -> to_date)],
        ];
    }

    public function messages()
    {
        return [
            'customer_branch_id.required' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.customer_branch_id'),
            'customer_branch_id.exists' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.customer_branch_id'),

            'from_date.required' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.date'),
            'from_date.date' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.date'),

            'to_date.required' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.date'),
            'to_date.date' => trans('form_responses.reports.sales.customer_branch_products_withdrawals.date'),
        ];
    }
}
