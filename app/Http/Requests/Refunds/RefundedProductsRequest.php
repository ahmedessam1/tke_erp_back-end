<?php

namespace App\Http\Requests\Refunds;

use App\Rules\Refunds\RefundOrderApprove;
use App\Rules\RefundToSupplierMaxQuantity;
use Illuminate\Foundation\Http\FormRequest;

class RefundedProductsRequest extends FormRequest
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
            'refund_id' => [
                'required',
                'exists:refunds,id',
                new RefundOrderApprove($this -> refund_id)
            ],
            'product_id' => 'required|exists:products,id',
            'quantity' => [
                'required',
                'integer',
                'min:'.trans('validation_standards.quantity.min'),
                // new RefundToSupplierMaxQuantity($this -> refund_id, $this -> product_id, $this -> quantity),
            ],
            'price' => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'refunded.required' => trans('form_responses.refunds_validation.products.refund_id.required'),
            'refunded.exists' => trans('form_responses.refunds_validation.products.refund_id.exists'),
            // PRODUCTS
            'product_id.required' => trans('form_responses.refunds_validation.products.product_id.required'),
            'product_id.exists' => trans('form_responses.refunds_validation.products.product_id.exists'),

            // PRODUCTS QUANTITY
            'quantity.required' => trans('form_responses.refunds_validation.products.quantity.required'),
            'quantity.integer' => trans('form_responses.refunds_validation.products.quantity.required'),
            'quantity.min' => trans('form_responses.refunds_validation.products.quantity.min'),

            // PRICE
            'price.required' => trans('form_responses.refunds_validation.products.price.required'),
        ];
    }
}
