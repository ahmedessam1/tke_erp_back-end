<?php

namespace App\Rules;

use App\Models\Invoices\ExportInvoice;
use Illuminate\Contracts\Validation\Rule;

class InvoicesNumberRule implements Rule
{
    protected $tax, $invoice_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($tax, $invoice_id)
    {
        $this->tax = $tax;
        $this->invoice_id = $invoice_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param $number
     * @return bool
     */
    public function passes($attribute, $number)
    {
        // CHECK IF INVOICE NUMBER EXISTS IF [NUMBER EXISTS, NOT DELETED, CHECK WHERE IT HAS TAX OR NOT]
        $check = ExportInvoice::where('id', '!=', $this->invoice_id)->where('number', $number)->whereNull('deleted_at')->where('tax', $this->tax)->first();
        if (!$check)
            return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.export_invoices_validation.number.unique');
    }
}
