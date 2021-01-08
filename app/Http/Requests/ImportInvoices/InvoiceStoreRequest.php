<?php

namespace App\Http\Requests\ImportInvoices;

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
            'supplier_id' => 'required
                            |exists:suppliers,id',
            'date' => 'required|date',
            'number' => 'required
                        |numeric
                        |unique:import_invoices,number,'.$this -> import_invoice_id.',id,deleted_at,NULL',
            'tax' => 'required|boolean',
            'discount' => 'numeric
                        |max:'.trans('validation_standards.discount.max').'
                        |min:'.trans('validation_standards.discount.min'),
        ];
    }

    public function messages()
    {
        return [
            // INVOICE INFO VALIDATION
            // NAME VALIDATION
            'name.required' => trans('form_responses.import_invoices_validation.name.required'),
            'name.max'      => trans('form_responses.import_invoices_validation.name.max'),
            'name.min'      => trans('form_responses.import_invoices_validation.name.min'),

            // SUPPLIER ID
            'supplier_id.required'     => trans('form_responses.import_invoices_validation.supplier_id.required'),
            'supplier_id.exists'       => trans('form_responses.import_invoices_validation.supplier_id.exists'),

            // DATE
            'date.required'    => trans('form_responses.import_invoices_validation.date.required'),
            'date.date'        => trans('form_responses.import_invoices_validation.date.date'),

            // NUMBER
            'number.required' => trans('form_responses.import_invoices_validation.number.required'),
            'number.unique'   => trans('form_responses.import_invoices_validation.number.unique'),
            'number.numeric'  => trans('form_responses.import_invoices_validation.number.numeric'),

            // TAX
            'tax.required' => trans('form_responses.import_invoices_validation.tax.required'),
            'tax.boolean'  => trans('form_responses.import_invoices_validation.tax.boolean'),

            // DISCOUNT
            'discount.numeric' => trans('form_responses.import_invoices_validation.discount.numeric'),
            'discount.max'     => trans('form_responses.import_invoices_validation.discount.max'),
            'discount.min'     => trans('form_responses.import_invoices_validation.discount.min'),
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
            'supplier_id'   => $this -> supplier_id,
            'tax'           => (int)$tax,
            'discount'      => (int)$discount,
            'date'          => $this -> date,
        ]);
    }
}
