<?php

namespace App\Rules\ExportInvoices;

use App\Models\Product\Product;
use App\Models\Product\SoldProducts;
use Illuminate\Contracts\Validation\Rule;

class QuantityMinValue implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $invoice_id, $product_id, $quantity, $available_quantity;

    public function __construct($invoice_id, $product_id, $quantity)
    {
        $this->invoice_id = $invoice_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
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
        // VALIDATING THE PRODUCT QUANTITY NOT TO EQUAL NEGATIVE VALUE
        $product = Product::withSold()->withCreditsAndWarehouses()->find($this->product_id);
        if ($product) {
            $product_available_quantity = $product->report_total_quantity;
            $this->available_quantity = $product_available_quantity;
            $requested_quantity = $this->quantity;
            if ($requested_quantity > $product_available_quantity)
                return false;
            else return true;
        } else return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.export_invoices_validation.quantity.max') . $this->available_quantity;
    }
}
