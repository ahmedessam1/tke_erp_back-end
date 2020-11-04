<?php

namespace App\Http\Requests\Reports\ProductsReports;

use App\Rules\ToDateMoreThanFromDateRule;
use Illuminate\Foundation\Http\FormRequest;

class HistoryRequest extends FormRequest
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
            'from_date' => 'required|date',
            'to_date' => ['required', 'date', new ToDateMoreThanFromDateRule($this -> from_date, $this -> to_date)],
            'product_id' => 'required|exists:products,id',
        ];
    }

    public function messages()
    {
        return [
            'from_date.required' => trans('form_responses.reports.products.history.date'),
            'from_date.date' => trans('form_responses.reports.products.history.date'),

            'to_date.required' => trans('form_responses.reports.products.history.date'),
            'to_date.date' => trans('form_responses.reports.products.history.date'),

            'product_id.required' => trans('form_responses.reports.products.history.product_id'),
            'product_id.exists' => trans('form_responses.reports.products.history.product_id'),
        ];
    }
}
