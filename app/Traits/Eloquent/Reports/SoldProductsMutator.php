<?php

namespace App\Traits\Eloquent\Reports;

use App\Models\Refund\Refund;
use App\Models\Refund\RefundProduct;

trait SoldProductsMutator {
    // SINGLE ITEM NET PRICE
    public function getItemNetPriceAttribute() {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $sold_price = $this->sold_price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->exportInvoice->tax;
        $invoice_discount = $this->exportInvoice->discount;

        $quantity = 1;
        $company_tax = false;
        $value = $this->calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['item_net_price'] = $value;
    }

    // SINGLE ITEM NET PRICE 'AFTER TAX'
    public function getItemNetPriceAfterTaxAttribute() {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $sold_price = $this->sold_price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->exportInvoice->tax;
        $invoice_discount = $this->exportInvoice->discount;

        $quantity = 1;
        $company_tax = true;
        $value = $this->calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['item_net_price_after_tax'] = $value;
    }

    // PRODUCT CREDIT IN EACH INVOICE
    public function getCreditNetPriceAttribute() {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $sold_price = $this->sold_price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->exportInvoice->tax;
        $invoice_discount = $this->exportInvoice->discount;

        $quantity = $this->quantity;
        $company_tax = false;
        $value = $this->calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['credit_net_price'] = $value;
    }

    // PRODUCT CREDIT IN EACH INVOICE 'AFTER TAX'
    public function getCreditNetPriceAfterTaxAttribute() {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $sold_price = $this->sold_price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->exportInvoice->tax;
        $invoice_discount = $this->exportInvoice->discount;

        $quantity = $this->quantity;
        $company_tax = true;
        $value = $this->calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['credit_net_price_after_tax'] = $value;
    }

    // SINGLE ITEM NET PRICE WITHOUT INVOICE TAX AND DISCOUNT
    public function getItemNetPriceWithoutInvoiceTaxAndDiscountAttribute() {
        // PRODUCT SOLD PRICE AND DISCOUNT
        $sold_price = $this->sold_price;
        $discount = $this->discount;

        // EXPORT INVOICE TAX AND DISCOUNT
        $tax = $this->exportInvoice->tax;
        $invoice_discount = $this->exportInvoice->discount;

        $quantity = 1;
        $company_tax = false;
        $value = $this->calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
        return $this->attributes['item_net_price_without_invoice_tax_and_discount'] = $value;
    }

    // CALCULATING THE NET PRICE HELPER FUNCTION
    private function calculateNetPrice($sold_price, $discount, $tax, $invoice_discount, $quantity, $company_tax) {
        // CALCULATE DISCOUNT
        $sold_price = $sold_price * $quantity;
        $discount_value = $sold_price * $discount / 100;
        $net_price = $sold_price - $discount_value;

        // CALCULATE INVOICE DISCOUNT
        $invoice_discount_value = $net_price * $invoice_discount / 100;
        $net_price -= $invoice_discount_value;

        // CALCULATE TAX
        if ($tax)
            $net_price *= config('constants.tax');

        if ($company_tax)
            $net_price *= config('constants.tax');
        return $net_price;
    }
}