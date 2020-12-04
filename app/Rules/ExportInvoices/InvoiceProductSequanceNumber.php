<?php

namespace App\Rules\ExportInvoices;

use App\Models\Product\Product;
use App\Models\Product\SoldProducts;
use Illuminate\Contracts\Validation\Rule;

class InvoiceProductSequanceNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $invoice_id, $sequence_number;

    public function __construct($invoice_id, $sequence_number)
    {
        $this->invoice_id = $invoice_id;
        $this->sequence_number = $sequence_number;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $checker = SoldProducts::where('export_invoice_id', $this->invoice_id)->where('sequence_number', $this->sequence_number)->first();
        if ($checker)
            return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.export_invoices_validation.sequence_number.duplicate');
    }
}
