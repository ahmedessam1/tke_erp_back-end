<?php

namespace App\Http\Requests;

use App\Rules\ExportInvoices\QuantityMinValue;
use App\Rules\ExportInvoices\SellerMatchBranch;
use App\Rules\ExportInvoices\SellingPriceMin;
use Illuminate\Foundation\Http\FormRequest;

class ExportInvoicesRequest extends FormRequest
{
    /**-=-=-=-=--=-=-=--=-=-=-=--=-=-=--=-=-=-=--=-=-=-
     * -=-=-=-=--=-=-=- DEPRECATED FILE =-=-=-=--=-=-=-
     * =-=-=-=--=-=-=-=-=-=-=--=-=-=-=-=-=-=--=-=-=-=-=
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
            // INVOICE INFO VALIDATION
            'invoice_data.name' => 'required
                                    |min:'.trans('validation_standards.names.min').'
                                    |max:'.trans('validation_standards.names.max'),

            'invoice_data.number' => 'required
                                    |numeric
                                    |unique:export_invoices,number,'.$this -> export_invoice_id,

            'invoice_data.customer_branch_id' => 'required|exists:customer_branches,id',

            'invoice_data.seller_id' => 'required|exists:users,id',

            'invoice_data.tax'          => 'required|boolean',

            'invoice_data.discount'     => 'numeric
                                            |max:'.trans('validation_standards.discount.max').'
                                            |min:'.trans('validation_standards.discount.min'),

            'invoice_data.date'         => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            // INVOICE INFO VALIDATION
            // NAME VALIDATION
            'invoice_data.name.required' => trans('form_responses.export_invoices_validation.name.required'),
            'invoice_data.name.max'      => trans('form_responses.export_invoices_validation.name.max'),
            'invoice_data.name.min'      => trans('form_responses.export_invoices_validation.name.min'),

            // NUMBER VALIDATION
            'invoice_data.number.required' => trans('form_responses.export_invoices_validation.number.required'),
            'invoice_data.number.unique'   => trans('form_responses.export_invoices_validation.number.unique'),
            'invoice_data.number.numeric'  => trans('form_responses.export_invoices_validation.number.numeric'),

            // CUSTOMER BRANCH ID VALIDATION
            'invoice_data.customer_branch_id.required' => trans('form_responses.export_invoices_validation.customer_branch_id.required'),
            'invoice_data.customer_branch_id.exists'   => trans('form_responses.export_invoices_validation.customer_branch_id.exists'),


            // BRANCH SELLER ID VALIDATION
            'invoice_data.seller_id.required' => trans('form_responses.export_invoices_validation.seller_id.required'),

            // DATE
            'invoice_data.date.required'    => trans('form_responses.export_invoices_validation.date.required'),
            'invoice_data.date.date'        => trans('form_responses.export_invoices_validation.date.date'),

            // TAX
            'invoice_data.tax.required' => trans('form_responses.export_invoices_validation.tax.required'),
            'invoice_data.tax.boolean'  => trans('form_responses.export_invoices_validation.tax.boolean'),

            // DISCOUNT
            'invoice_data.discount.numeric' => trans('form_responses.export_invoices_validation.discount.numeric'),
            'invoice_data.discount.max'     => trans('form_responses.export_invoices_validation.discount.max'),
            'invoice_data.discount.min'     => trans('form_responses.export_invoices_validation.discount.min'),


            // SOLD PRODUCTS
            'sold_products.required' => trans('form_responses.export_invoices_validation.sold_products.required'),
            // PRODUCT ID VALIDATION
            'sold_products.*.product_id.required' => trans('form_responses.export_invoices_validation.product_id.required'),
            'sold_products.*.product_id.exists'   => trans('form_responses.export_invoices_validation.product_id.exists'),

            // PRODUCT QUANTITY
            'sold_products.*.quantity.required' => trans('form_responses.export_invoices_validation.quantity.required'),
            'sold_products.*.quantity.integer'  => trans('form_responses.export_invoices_validation.quantity.numeric'),
            'sold_products.*.quantity.min'  => trans('form_responses.export_invoices_validation.quantity.min'),
            'sold_products.*.quantity.max'  => trans('form_responses.export_invoices_validation.quantity.max'),

            // PRODUCT DISCOUNT
            'sold_products.*.discount.numeric'   => trans('form_responses.export_invoices_validation.discount.numeric'),
            'sold_products.*.discount.max'       => trans('form_responses.export_invoices_validation.discount.max'),

            // SOLD PRODUCT PRICE
            'sold_products.*.sold_price.required' => trans('form_responses.export_invoices_validation.sold_price.required'),
            'sold_products.*.sold_price.numeric'  => trans('form_responses.export_invoices_validation.sold_price.numeric'),


        ];
    }

    protected function prepareForValidation()
    {
        // SETTING INVOICE_DATA DISCOUNT TO ZERO IF NULL AND INT
        $discount = $this->discount;
        $tax = $this->tax;
        if($discount == '' || $discount == null)
            $discount = 0;
        if($tax == '' || $tax == null)
            $tax = 0;
        $this->merge([
            'name'          => $this->name,
            'number'        => $this->number,
            'customer_branch_id' => $this->customer_branch_id,
            'seller_id'     => $this->seller_id,
            'tax'           => (int)$tax,
            'discount'      => (float)$discount,
            'date'          => $this->date,
        ]);
    }
}
