<?php

namespace App\Http\Requests;

use App\Rules\ProductDismiss\ProductDismissQuantityMax;
use Illuminate\Foundation\Http\FormRequest;

class ProductDismissOrderRequest extends FormRequest
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
            // PRODUCT DISMISS ORDER TITLE VALIDATION
            'title' => 'required
                       |min:'.trans('validation_standards.names.min').'
                       |max:'.trans('validation_standards.names.max'),

            'products' => 'required',

            'products.*.product_id' => 'required
                                        |exists:products,id',

            'products.*.quantity' => ['required', 'numeric', 'min:0', new ProductDismissQuantityMax($this -> products)],
            'products.*.reason_id' => 'required
                                        |exists:product_dismiss_reasons,id',

        ];
    }

    public function messages()
    {
        return [
            // PRODUCT DISMISS ORDER TITLE
            'title.required' => trans('form_responses.product_dismiss_order_validation.title.required'),
            'title.max'      => trans('form_responses.product_dismiss_order_validation.title.max'),
            'title.min'      => trans('form_responses.product_dismiss_order_validation.title.min'),

            // PRODUCTS
            'products.required' => trans('form_responses.product_dismiss_order_validation.products.required'),

            'products.*.product_id.required' => trans('form_responses.product_dismiss_order_validation.products.product_id.required'),
            'products.*.product_id.exists' => trans('form_responses.product_dismiss_order_validation.products.product_id.exists'),

            'products.*.quantity.required' => trans('form_responses.product_dismiss_order_validation.products.quantity.required'),
            'products.*.quantity.numeric' => trans('form_responses.product_dismiss_order_validation.products.quantity.numeric'),
            'products.*.quantity.min' => trans('form_responses.product_dismiss_order_validation.products.quantity.min'),

            'products.*.reason_id.required' => trans('form_responses.product_dismiss_order_validation.products.reason_id.required'),
            'products.*.reason_id.exists' => trans('form_responses.product_dismiss_order_validation.products.reason_id.exists'),
        ];
    }
}
