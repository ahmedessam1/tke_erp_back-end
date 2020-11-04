<?php

namespace App\Observers;

use App\Models\Refund\RefundProduct;
use App\Traits\Observers\Helpers\ProductsCalculations;
use App\Traits\Observers\InvoiceObserversTrait;

class RefundProductObserver
{
    use InvoiceObserversTrait, ProductsCalculations;

    /**
     * Handle the refund product "created" event.
     *
     * @param RefundProduct $refund_product
     * @return void
     */
    public function created(RefundProduct $refund_product)
    {
        $invoice = $refund_product->refundOrder;
        // ADD PRODUCT NET PRICE TO INVOICE
        $this->calculateInvoiceTotalAndStore('create', 'refund_invoice', $invoice, $refund_product);

        // REMOVING PRODUCT QUANTITY IF REFUNDING TO SUPPLIER
        if ($invoice->type == 'out')
            $this->updateProductActualQuantity('subtracting', $refund_product);
    }

    /**
     * Handle the refund product "updated" event.
     *
     * @param  \App\Models\Refund\RefundProduct  $refundProduct
     * @return void
     */
    public function updated(RefundProduct $refundProduct)
    {
        //
    }

    /**
     * Handle the refund product "deleted" event.
     *
     * @param RefundProduct $refund_product
     * @return void
     */
    public function deleted(RefundProduct $refund_product)
    {
        $invoice = $refund_product->refundOrder;
        // ADD PRODUCT NET PRICE TO INVOICE
        $this->calculateInvoiceTotalAndStore('delete', 'refund_invoice', $invoice, $refund_product);

        // ADDING PRODUCT QUANTITY IF REMOVE REFUNDING FROM SUPPLIER
        if ($invoice->type == 'out')
            $this->updateProductActualQuantity('adding', $refund_product);
    }

    /**
     * Handle the refund product "restored" event.
     *
     * @param  \App\Models\Refund\RefundProduct  $refundProduct
     * @return void
     */
    public function restored(RefundProduct $refundProduct)
    {
        //
    }

    /**
     * Handle the refund product "force deleted" event.
     *
     * @param  \App\Models\Refund\RefundProduct  $refundProduct
     * @return void
     */
    public function forceDeleted(RefundProduct $refundProduct)
    {
        //
    }
}
