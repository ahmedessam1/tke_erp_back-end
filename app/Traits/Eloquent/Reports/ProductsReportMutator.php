<?php

namespace App\Traits\Eloquent\Reports;


use App\Models\Product\ProductCredits;
use App\Models\Product\SoldProducts;
use App\Models\ProductDismissOrder\ProductDismissOrderProducts;
use App\Models\Refund\RefundProduct;

trait ProductsReportMutator {
    public function getDeletableAttribute() {
        $flag = true;
        $import_invoices = ProductCredits::where('product_id', $this->id)->exists();
        $export_invoices = SoldProducts::where('product_id', $this->id)->exists();
        $refund_invoices = RefundProduct::where('product_id', $this->id)->exists();
        $product_dismissal = ProductDismissOrderProducts::where('product_id', $this->id)->exists();
        if ($import_invoices || $export_invoices || $refund_invoices || $product_dismissal)
            $flag = false;
        return $flag;
    }

    // SINGLE ITEM NET PRICE
    public function getReportTotalQuantityAttribute() {
        if ($this->hasRole(['super_admin', 'sales', 'data_entry'])) {
            return $this->productLog->available_quantity;
        } else
            return;
    }

    public function getReportAvgPurchasePriceAttribute() {
        if ($this->hasRole(['super_admin']))
            return $this->productLog->average_purchase_price;
        else
            return;
    }

    public function getReportTotalCreditAttribute() {
        if ($this->hasRole(['super_admin'])) {
            // GET THE PRODUCT QUANTITY AFTER SOLD
            $total_quantity = $this->productLog->available_quantity;

            // CALCULATE THE PRODUCT CREDIT
            $avg_purchase_price = $this->productLog->average_purchase_price;

            return $total_quantity * $avg_purchase_price;
        } else
            return;
    }


    /* ********************************************************* *
     * ******************* PRIVATE FUNCTIONS ******************* *
     * ********************************************************* */
}
