<?php

namespace App\Http\Requests\Reports\ProductsReports;

use Illuminate\Foundation\Http\FormRequest;

class CategorySalesReportRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'from_date.required' => trans('form_responses.reports.products.category_sales.date'),
            'from_date.date' => trans('form_responses.reports.products.category_sales.date'),

            'to_date.required' => trans('form_responses.reports.products.category_sales.date'),
            'to_date.date' => trans('form_responses.reports.products.category_sales.date'),

            'category_id.required' => trans('form_responses.reports.products.category_sales.category_id'),
            'category_id.exists' => trans('form_responses.reports.products.category_sales.category_id'),
        ];
    }
}
