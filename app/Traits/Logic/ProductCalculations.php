<?php

namespace App\Traits\Logic;

use App\Traits\Eloquent\FieldsPermission;

trait ProductCalculations {
    /* ***************************************************************************
     * ************************ DEPRECATED CLASS TILL NOW ************************
     * **************************************************************************/
    use FieldsPermission;

    // PRODUCT NET SOLD PRICE
    public function productNetSoldPrice ($sold_price, $discount, $invoice_tax, $invoice_discount, $quantity) {
        // CALCULATE DISCOUNT
        $sold_price = $sold_price * $quantity;
        $discount_value = $sold_price * $discount / 100;
        $net_price = $sold_price - $discount_value;

        // CALCULATE INVOICE DISCOUNT
        $invoice_discount_value = $net_price * $invoice_discount / 100;
        $net_price -= $invoice_discount_value;

        // CALCULATE TAX
        if ($invoice_tax)
            $net_price *= config('constants.tax');

        return $net_price;
    }

    // PRODUCT ACTUAL PRICE STOCK IN WAREHOUSES 'BEFORE TAX'
    public function creditPriceInWarehousesBeforeTax ($credits) {
        $total = $this -> creditPriceInWarehouses($credits, false);
        return $total;
    }

    // PRODUCT ACTUAL PRICE STOCK IN WAREHOUSES 'AFTER TAX'
    public function creditPriceInWarehousesAfterTax ($credits) {
        $total = $this -> creditPriceInWarehouses($credits, true);
        return $total;
    }

    // PACKAGE NUMBER
    public function packageSize ($quantity, $package_size) {
        return $quantity / $package_size;
    }

    /* ********************************************************* *
     * ******************* PRIVATE FUNCTIONS ******************* *
     * ********************************************************* */
    private function creditPriceInWarehouses ($credits, $tax) {
        if ($this -> hasRole(['super_admin'])) {
            $counter = count($credits);
            $total = 0;
            for ($x = 0; $x < $counter; $x++)
                $total += ($credits[$x]->item_net_price * $credits[$x]->quantity);

            if ($tax)
                $total *= config('constants.tax');
            return $total;
        } else
            return;
    }
}
