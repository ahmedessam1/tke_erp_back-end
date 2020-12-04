<?php

namespace App\Http\Requests\ExportInvoices;

use App\Rules\ExportInvoices\InvoiceProductSequanceNumber;
use App\Rules\ExportInvoices\QuantityMinValue;
use App\Rules\ExportInvoices\SellingPriceMin;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceProductsStoreRequest extends FormRequest
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
            'invoice_id' => 'required|exists:export_invoices,id',

            'sequence_number' => ['required', new InvoiceProductSequanceNumber($this->invoice_id, $this->sequence_number)],

            // SOLD PRODUCTS VALIDATION
            'product_id' => 'required|exists:products,id',
            'quantity' => [
                'required',
                'integer',
                'min:'.trans('validation_standards.quantity.min'),
//                new QuantityMinValue(
//                    $this -> invoice_id,
//                    $this -> product_id,
//                    $this -> quantity,
//                    $this -> _method
//                ),
            ],
            'sold_price' => [
                'required',
                'numeric',
//                new SellingPriceMin(
//                    $this -> invoice_id,
//                    $this -> product_id,
//                    $this -> sold_price,
//                    $this -> discount
//                )
            ],
            'discount' => 'numeric
                            |max:'.trans('validation_standards.discount.max').'
                            |min:'.trans('validation_standards.discount.min'),
        ];
    }

    public function messages()
    {
        return [

            // SOLD PRODUCTS
            'required' => trans('form_responses.export_invoices_validation.sold_products.required'),
            // PRODUCT ID VALIDATION
            'product_id.required' => trans('form_responses.export_invoices_validation.product_id.required'),
            'product_id.exists'   => trans('form_responses.export_invoices_validation.product_id.exists'),

            // PRODUCT QUANTITY
            'quantity.required' => trans('form_responses.export_invoices_validation.quantity.required'),
            'quantity.integer'  => trans('form_responses.export_invoices_validation.quantity.numeric'),
            'quantity.min'  => trans('form_responses.export_invoices_validation.quantity.min'),

            // PRODUCT DISCOUNT
            'discount.numeric'   => trans('form_responses.export_invoices_validation.discount.numeric'),
            'discount.max'       => trans('form_responses.export_invoices_validation.discount.max'),

            // SOLD PRODUCT PRICE
            'sold_price.required' => trans('form_responses.export_invoices_validation.sold_price.required'),
            'sold_price.numeric'  => trans('form_responses.export_invoices_validation.sold_price.numeric'),

            // SEQUENCE NUMBER
            'sequence_number.required' => trans('form_responses.export_invoices_validation.sequence_number.required'),
        ];
    }

    protected function prepareForValidation()
    {
        // SETTING SOLD_PRODUCT DISCOUNT TO ZERO IF NULL AND INT
        $product_discount = $this -> discount;
        if ($product_discount == '' || $product_discount == null)
            $product_discount = 0;
        $this -> merge([
            'product_id'    => $this -> product_id,
            'quantity'      => (int)$this -> quantity,
            'sold_price'    => $this -> sold_price,
            'discount'      => (float)$product_discount,
        ]);
    }
}
