<?php

namespace App\Traits\Logic;

trait InvoiceCalculations {
    /**
     * @param $type
     * @param $credits
     * @param $tax
     * @param $invoice_discount
     * @return float|\Illuminate\Config\Repository|int|mixed
     */
    public function invoiceTotal ($type, $credits, $tax, $invoice_discount) {
        $total = 0;
        $counter = count($credits);
        for ($i = 0; $i < $counter; $i++) {
            $item_price = 0;
            if ($type === 'import_invoice')
                $item_price = $credits[$i]->purchase_price;
            else if ($type === 'export_invoice')
                $item_price = $credits[$i]->sold_price;
            else if ($type === 'refund_invoice')
                $item_price = $credits[$i]->price;

            $percentage_value = ($item_price * $credits[$i]->discount / 100) * $credits[$i]->quantity;
            $total += ($item_price * $credits[$i]->quantity);
            $total -= $percentage_value;
        }

        // CALCULATE INVOICE DISCOUNT
        if ($invoice_discount > 0) {
            $invoice_discount_value = $total * $invoice_discount / 100;
            $total -= $invoice_discount_value;
        }

        if ($tax)
            $total *= config('constants.tax');
        return $total;
    }

    /**
     * @param $total
     * @param $discount
     * @return float|int
     */
    public function calculateTotalAfterDiscount ($total, $discount) {
        if ($discount > 0) {
            $invoice_discount_value = $total * $discount / 100;
            $total -= $invoice_discount_value;
        }
        return $total;
    }

    /**
     * @param $total
     * @param $tax
     * @param $discount
     * @return \Illuminate\Config\Repository|mixed
     */
    public function calculateTotalAfterTax ($total, $tax, $discount) {
        $total_after_discount = $this->calculateTotalAfterDiscount($total, $discount);
        if ($tax)
            $total_after_discount *= config('constants.tax');
        return $total_after_discount;
    }


    /* ********************************************************* *
     * ******************* PRIVATE FUNCTIONS ******************* *
     * ********************************************************* */

}
