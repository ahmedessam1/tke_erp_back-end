<?php

namespace App\Traits\Eloquent\Products;


use App\Models\Product\ProductCredits;
use App\Models\Product\SoldProducts;
use App\Models\ProductDismissOrder\ProductDismissOrderProducts;
use App\Models\Refund\RefundProduct;

trait ProductCalculationsTrait
{
    public function calculateProductAvailableQuantity($product_id)
    {
        // GET IMPORT INVOICE SUM
        $import_invoices_sum = ProductCredits::where('product_id', $product_id)
            ->where(function ($q) {
                $q->whereNull('import_invoice_id');
                $q->orWhereHas('importInvoice', function ($query) {
                    $query->approved();
                });
            })
            ->sum('quantity');

        // GET EXPORT INVOICE SUM
        $export_invoices_sum = SoldProducts::where('product_id', $product_id)->sum('quantity');

        // GET "VALID" REFUNDED QUANTITIES
        $refunded_quantities = RefundProduct::with('refundOrder')
            ->whereHas('refundOrder', function ($query) {
                $query->approved();
            })
            ->where('product_id', $product_id)
            ->get();
        $refund_from_customer = [];
        $refund_from_supplier = [];
        foreach ($refunded_quantities as $refunded_quantity_quantity) {
            if ($refunded_quantity_quantity->refundOrder) {
                if ($refunded_quantity_quantity->refundOrder->type === 'in') {
                    if ($refunded_quantity_quantity->valid)
                        array_push($refund_from_customer, $refunded_quantity_quantity->quantity);
                } else {
                    array_push($refund_from_supplier, $refunded_quantity_quantity->quantity);
                }
            }
        }

        // GET THE DISMISSED PRODUCTS QUANTITY
        $dismissed_quantity = ProductDismissOrderProducts::where('product_id', $product_id)->sum('quantity');

        // CALCULATE THE SUM
        return ($import_invoices_sum + array_sum($refund_from_customer)) - ($export_invoices_sum + $dismissed_quantity + array_sum($refund_from_supplier));
    }

    public function calculateProductAvgPurchasePrice($product_id)
    {
        /*
        $purchase_products = ProductCredits::where('product_id', $product_id)
            ->where(function ($q) {
                $q->whereHas('importInvoice', function ($query) {
                    $query->approved();
                });
            })
            ->orderBy('id', 'DESC')
            ->limit(2)
            ->get();

        $total = [];
        foreach ($purchase_products as $p_p) {
            $product_net_price = $this->productNetPrice('purchase', $p_p);
            $sum = $product_net_price * $p_p->quantity;
            array_push($total, $sum);
        }
        $sum_total = array_sum($total);

        if ($sum_total > 0) {
            $average_purchase_price = $sum_total / $purchase_products->sum('quantity');
            return $average_purchase_price;
        }
        */
        $purchase_products = ProductCredits::where('product_id', $product_id)->orderBy('id', 'DESC')->where(function ($q) {
                $q->whereHas('importInvoice', function ($query) {
                    $query->approved();
                });
            })->limit(1)->first();
        $product_net_price = $this->productNetPrice('purchase', $purchase_products);
        return $product_net_price;
    }

    public function calculateProductAvgSellPrice($product_id)
    {
        $sold_products = SoldProducts::where('product_id', $product_id)
            ->whereHas('exportInvoice', function ($query) {
                $query->Approved();
            })
            ->orderBy('id', 'DESC')
            ->limit(2)
            ->get();

        $total = [];
        foreach ($sold_products as $s_p) {
            $product_net_price = $this->productNetPrice('sell', $s_p);
            $sum = $product_net_price * $s_p->quantity;
            array_push($total, $sum);
        }
        $sum_total = array_sum($total);

        if ($sum_total > 0) {
            $average_sold_price = $sum_total / $sold_products->sum('quantity');
            return $average_sold_price;
        }
        return 0;
    }

    /**********************************************
     * ************* PRIVATE HELPERS **************
     *********************************************/
    private function productNetPrice($type, $product)
    {
        $price = 0;
        // PRODUCT PURCHASE PRICE AND DISCOUNT
        if ($type === 'purchase') {
            $price = $product->purchase_price;
        } else if ($type === 'sell') {
            $price = $product->sold_price;
        }
        $discount = $product->discount;


        // IMPORT INVOICE TAX AND DISCOUNT
        $tax = null;
        $invoice_discount = null;
        if ($type === 'purchase') {
            if ($product->importInvoice) {
                $tax = $product->importInvoice->tax;
                $invoice_discount = $product->importInvoice->discount;
            }
        } else if ($type === 'sell') {
            $tax = $product->exportInvoice->tax;
            $invoice_discount = $product->exportInvoice->discount;
        }

        $quantity = 1;
        $company_tax = false;
        return $this->calculateNetPrice($price, $discount, $tax, $invoice_discount, $quantity, $company_tax);
    }

    private function calculateNetPrice($purchase_price, $discount, $tax, $invoice_discount, $quantity, $company_tax)
    {
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
