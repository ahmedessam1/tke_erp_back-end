<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportInvoicesRequest extends FormRequest
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
            'invoice_data.name'         => 'required
                                            |min:'.trans('validation_standards.names.min').'
                                            |max:'.trans('validation_standards.names.max'),
            'invoice_data.supplier_id'  => 'required
                                            |exists:suppliers,id',
            'invoice_data.date'         => 'required|date',
            'invoice_data.number'       => 'required
                                            |numeric
                                            |unique:import_invoices,number,'.$this -> import_invoice_id,
            'invoice_data.tax'          => 'required|boolean',
            'invoice_data.discount'     => 'numeric
                                            |max:'.trans('validation_standards.discount.max').'
                                            |min:'.trans('validation_standards.discount.min'),
        ];
    }

    public function messages()
    {
        return [
            // INVOICE INFO VALIDATION
            // NAME VALIDATION
            'invoice_data.name.required' => trans('form_responses.import_invoices_validation.name.required'),
            'invoice_data.name.max'      => trans('form_responses.import_invoices_validation.name.max'),
            'invoice_data.name.min'      => trans('form_responses.import_invoices_validation.name.min'),

            // SUPPLIER ID
            'invoice_data.supplier_id.required'     => trans('form_responses.import_invoices_validation.supplier_id.required'),
            'invoice_data.supplier_id.exists'       => trans('form_responses.import_invoices_validation.supplier_id.exists'),

            // DATE
            'invoice_data.date.required'    => trans('form_responses.import_invoices_validation.date.required'),
            'invoice_data.date.date'        => trans('form_responses.import_invoices_validation.date.date'),

            // NUMBER
            'invoice_data.number.required' => trans('form_responses.import_invoices_validation.number.required'),
            'invoice_data.number.unique'   => trans('form_responses.import_invoices_validation.number.unique'),
            'invoice_data.number.numeric'  => trans('form_responses.import_invoices_validation.number.numeric'),

            // TAX
            'invoice_data.tax.required' => trans('form_responses.import_invoices_validation.tax.required'),
            'invoice_data.tax.boolean'  => trans('form_responses.import_invoices_validation.tax.boolean'),

            // DISCOUNT
            'invoice_data.discount.numeric' => trans('form_responses.import_invoices_validation.discount.numeric'),
            'invoice_data.discount.max'     => trans('form_responses.import_invoices_validation.discount.max'),
            'invoice_data.discount.min'     => trans('form_responses.import_invoices_validation.discount.min'),
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
            'supplier_id'   => $this->supplier_id,
            'tax'           => (int)$tax,
            'discount'      => (int)$discount,
            'date'          => $this->date,
        ]);
    }
}
