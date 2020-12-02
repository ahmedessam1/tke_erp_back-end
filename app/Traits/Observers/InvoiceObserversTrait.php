<?php

namespace App\Traits\Observers;

use App\Models\Invoices\ExportInvoice;
use App\Models\Invoices\ImportInvoice;

trait InvoiceObserversTrait {
    public function calculateInvoiceTotalAndStore ($method, $type, $invoice, $product) {
        $product_net_price = null;
        $invoice_tax = null;
        $invoice_discount = null;
        $product_price = null;
        if ($type === 'import_invoice')
            $product_price = $product->purchase_price;
        else if ($type === 'export_invoice')
            $product_price = $product->sold_price;
        else if ($type === 'refund_invoice')
            $product_price = $product->price;
        $product_net_price = $this->calculateProductNetPrice($product_price, $product->quantity, $product->discount, $invoice_tax, $invoice_discount);
        if ($method === 'create')
            $invoice->net_total += $product_net_price;
        else if ($method === 'delete')
            $invoice->net_total -= $product_net_price;
        $invoice->save();

        return;
    }

    public function calculateSingleImportInvoice($invoice_id) {
        $invoice = ImportInvoice::withProductCredits()->find($invoice_id);
        $credits = $invoice->productCredits;
        $total = 0;
        for($x = 0; $x < count($credits); $x++) {
            $percentage_value = ($credits[$x]->purchase_price * $credits[$x]->discount / 100) * $credits[$x]->quantity;
            $total += ($credits[$x]->purchase_price * $credits[$x]->quantity);
            $total -= $percentage_value;
        }
        $invoice->net_total = $total;
        $invoice->save();
        return;
    }

    public function calculateSingleExportInvoice($invoice_id) {
        $invoice = ExportInvoice::with('soldProducts')->find($invoice_id);
        $credits = $invoice->soldProducts;
        $total = 0;
        for($x = 0; $x < count($credits); $x++) {
            $percentage_value = ($credits[$x]->sold_price * $credits[$x]->discount / 100) * $credits[$x]->quantity;
            $total += ($credits[$x]->sold_price * $credits[$x]->quantity);
            $total -= $percentage_value;
        }
        $invoice->net_total = $total;
        $invoice->save();
        return;
    }

    /* ********************************************************* *
     * ******************* PRIVATE FUNCTIONS ******************* *
     * ********************************************************* */
    private function calculateProductNetPrice ($product_price, $quantity, $product_discount, $invoice_tax, $invoice_discount) {
        $percentage_value = ($product_price * $product_discount / 100) * $quantity;
        $total = $product_price * $quantity;
        $total -= $percentage_value;

        if ($invoice_discount > 0) {
            $invoice_discount_value = $total * $invoice_discount / 100;
            $total -= $invoice_discount_value;
        }

        if ($invoice_tax)
            $total *= config('constants.tax');

        return $total;
    }

}
