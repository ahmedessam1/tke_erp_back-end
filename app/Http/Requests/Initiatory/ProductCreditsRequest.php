<?php

namespace App\Http\Requests\Initiatory;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreditsRequest extends FormRequest
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
            'products_credit'                     => 'required',
            'products_credit.*.product_id'        => 'required|exists:products,id',
            'products_credit.*.quantity'          => 'required|numeric',
            'products_credit.*.package_size'      => 'nullable|numeric',
            'products_credit.*.purchase_price'    => 'required|numeric',
            'products_credit.*.discount'          => 'numeric
                                                    |max:'.trans('validation_standards.discount.max').'
                                                    |min:'.trans('validation_standards.discount.min'),
            'products_credit.*.warehouse_id'      => 'required|exists:warehouses,id',
        ];
    }

    public function messages()
    {
        return [
            // PRODUCT CREDIT VALIDATION
            'products_credit.required'      => trans('form_responses.import_invoices_validation.products_credit.required'),
            // PRODUCT ID VALIDATION
            'products_credit.*.product_id.required'   => trans('form_responses.import_invoices_validation.product_id.required'),
            'products_credit.*.product_id.exists'     => trans('form_responses.import_invoices_validation.product_id.exists'),

            // PRODUCT QUANTITY
            'products_credit.*.quantity.required' => trans('form_responses.import_invoices_validation.quantity.required'),
            'products_credit.*.quantity.numeric'  => trans('form_responses.import_invoices_validation.quantity.numeric'),

            // PRODUCT PACKAGE SIZE
            'products_credit.*.package_size.numeric'  => trans('form_responses.import_invoices_validation.package_size.numeric'),

            // PRODUCT PURCHASE PRICE
            'products_credit.*.purchase_price.required' => trans('form_responses.import_invoices_validation.purchase_price.required'),
            'products_credit.*.purchase_price.numeric'  => trans('form_responses.import_invoices_validation.purchase_price.numeric'),

            // PRODUCT DISCOUNT
            'products_credit.*.discount.numeric'   => trans('form_responses.import_invoices_validation.discount.numeric'),
            'products_credit.*.discount.max'       => trans('form_responses.import_invoices_validation.discount.max'),

            // PRODUCT WAREHOUSE ID
            'products_credit.*.warehouse_id.required'  => trans('form_responses.import_invoices_validation.warehouse_id.required'),
            'products_credit.*.warehouse_id.exists'    => trans('form_responses.import_invoices_validation.warehouse_id.exists'),
        ];
    }

    protected function prepareForValidation() {
        // SETTING PRODUCTS_CREDIT DISCOUNT TO ZERO IF NULL AND INT
        $holder = [];
        foreach($this -> products_credit as $key => $product) {
            $product_discount = $product['discount'];
            if ($product_discount == '' || $product_discount == null)
                $product_discount = 0;
            array_push($holder, [
                'product_id'    => $product['product_id'],
                'quantity'      => $product['quantity'],
                'package_size'  => $product['package_size'],
                'discount'      => (int)$product_discount,
                'purchase_price'=> $product['purchase_price'],
                'warehouse_id'  => $product['warehouse_id'],
            ]);
        }
        $this -> merge([
            "products_credit" => $holder
        ]);
    }
}
