<?php

namespace App\Http\Requests\ImportInvoices;

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
            'invoice_id'        => 'required|exists:import_invoices,id',
            'product_id'        => 'required|exists:products,id',
            'quantity'          => 'required
                                                    |integer
                                                    |max:'.trans('validation_standards.quantity.max').'
                                                    |min:'.trans('validation_standards.quantity.min'),
            'package_size'      => 'nullable|numeric',
            'purchase_price'    => 'required|numeric',
            'discount'          => 'numeric
                                                    |max:'.trans('validation_standards.discount.max').'
                                                    |min:'.trans('validation_standards.discount.min'),
            'warehouse_id'      => 'required|exists:warehouses,id',
        ];
    }

    public function messages()
    {
        return [
            // IMPORT INVOICE ID VALIDATION
            'import_invoice_id.required'   => trans('form_responses.import_invoices_validation.import_invoice_id.required'),
            'import_invoice_id.exists'   => trans('form_responses.import_invoices_validation.import_invoice_id.exists'),

            // PRODUCT ID VALIDATION
            'product_id.required'   => trans('form_responses.import_invoices_validation.product_id.required'),
            'product_id.exists'     => trans('form_responses.import_invoices_validation.product_id.exists'),

            // PRODUCT QUANTITY
            'quantity.required' => trans('form_responses.import_invoices_validation.quantity.required'),
            'quantity.integer'  => trans('form_responses.import_invoices_validation.quantity.numeric'),
            'quantity.min'  => trans('form_responses.import_invoices_validation.quantity.min'),
            'quantity.max'  => trans('form_responses.import_invoices_validation.quantity.max'),

            // PRODUCT PACKAGE SIZE
            'package_size.numeric'  => trans('form_responses.import_invoices_validation.package_size.numeric'),

            // PRODUCT PURCHASE PRICE
            'purchase_price.required' => trans('form_responses.import_invoices_validation.purchase_price.required'),
            'purchase_price.numeric'  => trans('form_responses.import_invoices_validation.purchase_price.numeric'),

            // PRODUCT DISCOUNT
            'discount.numeric'   => trans('form_responses.import_invoices_validation.discount.numeric'),
            'discount.max'       => trans('form_responses.import_invoices_validation.discount.max'),

            // PRODUCT WAREHOUSE ID
            'warehouse_id.required'  => trans('form_responses.import_invoices_validation.warehouse_id.required'),
            'warehouse_id.exists'    => trans('form_responses.import_invoices_validation.warehouse_id.exists'),
        ];
    }

    protected function prepareForValidation()
    {
        // SETTING PRODUCTS_CREDIT DISCOUNT TO ZERO IF NULL AND INT
        $product_discount = $this -> discount;
        if ($product_discount == '' || $product_discount == null)
            $product_discount = 0;
        $this -> merge([
            'product_id'    => $this -> product_id,
            'quantity'      => (int)$this -> quantity,
            'package_size'  => $this -> package_size,
            'discount'      => (float)$product_discount,
            'purchase_price'=> $this -> purchase_price,
            'warehouse_id'  => $this -> warehouse_id,
        ]);
    }
}
