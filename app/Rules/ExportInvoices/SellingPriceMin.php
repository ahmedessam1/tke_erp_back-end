<?php

namespace App\Rules\ExportInvoices;

use App\Models\Invoices\ExportInvoice;
use App\Models\Product\Product;
use App\Traits\Logic\ProductCalculations;
use Illuminate\Contracts\Validation\Rule;

class SellingPriceMin implements Rule
{
    use ProductCalculations;

    private $invoice_id, $product_id, $sold_price, $discount;

    public function __construct($invoice_id, $product_id, $sold_price, $discount)
    {
        $this -> invoice_id = $invoice_id;
        $this -> product_id = $product_id;
        $this -> sold_price = $sold_price;
        $this -> discount = $discount;
    }

    public function passes($attribute, $value)
    {
        $invoice = ExportInvoice::notApproved() -> find($this -> invoice_id);
        $sold_price = $this -> sold_price;
        $product_id = $this -> product_id;
        $discount = $this -> discount;

        // CHECK IF THE PRODUCT IS NOT A GIFT
        if ($sold_price !== 0 && $sold_price !== '0') {
            // GET PRODUCT NET SOLD PRICE
            $product_net_sold_price = $this -> productNetSoldPrice(
                $sold_price,
                $discount,
                $invoice -> tax,
                $invoice -> discount,
                1
            );

            // GET PRODUCT AVERAGE PURCHASE PRICE
            $product = Product::withSold() -> withCreditsAndWarehouses() -> find($product_id);
            $product_avg_purchase_price = $product -> report_avg_purchase_price;

            if ($product_net_sold_price < $product_avg_purchase_price)
                return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('form_responses.export_invoices_validation.sold_price.min');
    }
}
