<?php

namespace App\Traits\Eloquent\Products;


trait ProductsMutator {
    // SINGLE ITEM NET PRICE
    public function getItemNetPriceAttribute() {
        if ($this -> hasRole(['super_admin'])) {
            // PRODUCT PURCHASE PRICE AND DISCOUNT
            $purchase_price = $this->purchase_price;
            $discount = $this->discount;

            // IMPORT INVOICE TAX AND DISCOUNT
            $tax = null;
            $invoice_discount = null;
            if ($this->importInvoice) {
                $tax = $this->importInvoice->tax;
                $invoice_discount = $this->importInvoice->discount;
            }

            $quantity = 1;
            $company_tax = false;
            $value = $this->calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
            return $this->attributes['item_net_price'] = $value;
        } else
            return;
    }

    // SINGLE ITEM NET PRICE 'AFTER TAX'
    public function getItemNetPriceAfterTaxAttribute() {
        if ($this -> hasRole(['super_admin'])) {
            // PRODUCT PURCHASE PRICE AND DISCOUNT
            $purchase_price = $this->purchase_price;
            $discount = $this->discount;

            // IMPORT INVOICE TAX AND DISCOUNT
            $tax = null;
            $invoice_discount = null;
            if ($this->importInvoice) {
                $tax = $this->importInvoice->tax;
                $invoice_discount = $this->importInvoice->discount;
            }
            $quantity = 1;
            $company_tax = true;
            $value = $this->calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
            return $this->attributes['item_net_price_after_tax'] = $value;
        } else
            return;
    }

    // PRODUCT CREDIT IN EACH INVOICE
    public function getCreditNetPriceAttribute() {
        if ($this -> hasRole(['super_admin'])) {
            // PRODUCT PURCHASE PRICE AND DISCOUNT
            $purchase_price = $this->purchase_price;
            $discount = $this->discount;

            // IMPORT INVOICE TAX AND DISCOUNT
            $tax = null;
            $invoice_discount = null;
            if ($this->importInvoice) {
                $tax = $this->importInvoice->tax;
                $invoice_discount = $this->importInvoice->discount;
            }

            $quantity = $this->quantity;
            $company_tax = false;
            $value = $this->calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
            return $this->attributes['credit_net_price'] = $value;
        } else
            return;
    }

    // PRODUCT CREDIT IN EACH INVOICE 'AFTER TAX'
    public function getCreditNetPriceAfterTaxAttribute() {
        if ($this -> hasRole(['super_admin'])) {
            // PRODUCT PURCHASE PRICE AND DISCOUNT
            $purchase_price = $this->purchase_price;
            $discount = $this->discount;

            // IMPORT INVOICE TAX AND DISCOUNT
            $tax = null;
            $invoice_discount = null;
            if ($this->importInvoice) {
                $tax = $this->importInvoice->tax;
                $invoice_discount = $this->importInvoice->discount;
            }

            $quantity = $this->quantity;
            $company_tax = true;
            $value = $this->calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
            return $this->attributes['credit_net_price_after_tax'] = $value;
        } else
            return;
    }

    // SINGLE ITEM NET PRICE WITHOUT INVOICE TAX AND DISCOUNT
    public function getItemNetPriceWithoutInvoiceTaxAndDiscountAttribute() {
        if ($this -> hasRole(['super_admin'])) {
            // PRODUCT PURCHASE PRICE AND DISCOUNT
            $purchase_price = $this->purchase_price;
            $discount = $this->discount;

            // IMPORT INVOICE TAX AND DISCOUNT
            $tax = 0;
            $invoice_discount = 0;

            $quantity = 1;
            $company_tax = false;
            $value = $this->calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
            return $this->attributes['item_net_price_without_invoice_tax_and_discount'] = $value;
        } else
            return;
    }

    // CALCULATING THE NET PRICE HELPER FUNCTION
    private function calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax) {
        // CALCULATE DISCOUNT
        $purchase_price = $purchase_price * $quantity;
        $discount_value = $purchase_price * $discount / 100;
        $net_price = $purchase_price - $discount_value;

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
