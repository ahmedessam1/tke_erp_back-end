<?php

namespace App\Http\Requests\ExportInvoices;

use App\Rules\ExportInvoices\SellerMatchBranch;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
            // INVOICE INFO VALIDATION
            'name' => 'required
                    |min:'.trans('validation_standards.names.min').'
                    |max:'.trans('validation_standards.names.max'),
            'number' => 'required
                        |numeric
                        |unique:export_invoices,number,'.$this -> export_invoice_id.',id,deleted_at,NULL',
            'customer_branch_id' => 'required|exists:customer_branches,id',
            'seller_id' => 'required|exists:users,id',
            'tax' => 'required|boolean',
            'discount' => 'numeric
                            |max:'.trans('validation_standards.discount.max').'
                            |min:'.trans('validation_standards.discount.min'),
            'date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            // NAME VALIDATION
            'name.required' => trans('form_responses.export_invoices_validation.name.required'),
            'name.max'      => trans('form_responses.export_invoices_validation.name.max'),
            'name.min'      => trans('form_responses.export_invoices_validation.name.min'),

            // NUMBER VALIDATION
            'number.required' => trans('form_responses.export_invoices_validation.number.required'),
            'number.unique'   => trans('form_responses.export_invoices_validation.number.unique'),
            'number.numeric'  => trans('form_responses.export_invoices_validation.number.numeric'),

            // CUSTOMER BRANCH ID VALIDATION
            'customer_branch_id.required' => trans('form_responses.export_invoices_validation.customer_branch_id.required'),
            'customer_branch_id.exists'   => trans('form_responses.export_invoices_validation.customer_branch_id.exists'),


            // BRANCH SELLER ID VALIDATION
            'seller_id.required' => trans('form_responses.export_invoices_validation.seller_id.required'),

            // DATE
            'date.required'    => trans('form_responses.export_invoices_validation.date.required'),
            'date.date'        => trans('form_responses.export_invoices_validation.date.date'),

            // TAX
            'invoice_data.tax.required' => trans('form_responses.export_invoices_validation.tax.required'),
            'invoice_data.tax.boolean'  => trans('form_responses.export_invoices_validation.tax.boolean'),

            // DISCOUNT
            'discount.numeric' => trans('form_responses.export_invoices_validation.discount.numeric'),
            'discount.max'     => trans('form_responses.export_invoices_validation.discount.max'),
            'discount.min'     => trans('form_responses.export_invoices_validation.discount.min'),
        ];
    }

    protected function prepareForValidation()
    {
        // SETTING INVOICE_DATA DISCOUNT TO ZERO IF NULL AND INT
        $discount = $this -> discount;
        $tax = $this -> tax;
        if($discount == '' || $discount == null)
            $discount = 0;
        if($tax == '' || $tax == null)
            $tax = 0;
        $this->merge([
            'name'          => $this -> name,
            'number'        => $this -> number,
            'customer_branch_id' => $this -> customer_branch_id,
            'seller_id'     => $this -> seller_id,
            'tax'           => (int)$tax,
            'discount'      => (float)$discount,
            'date'          => $this -> date,
        ]);
    }
}
