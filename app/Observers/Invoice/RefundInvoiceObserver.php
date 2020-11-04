<?php

namespace App\Observers\Invoice;

use App\Models\Refund\Refund;
use App\Traits\Observers\Helpers\ProductsCalculations;

class RefundInvoiceObserver
{
    use ProductsCalculations;
    /**
     * Handle the refund "created" event.
     *
     * @param Refund $refund
     * @return void
     */
    public function created(Refund $refund)
    {
        //
    }

    /**
     * Handle the refund "updated" event.
     *
     * @param Refund $refund
     * @return void
     */
    public function updated(Refund $refund)
    {
        // UPDATING PRODUCTS QUANTITY IF APPROVED
        if ($refund->approve == '1') {
            // GET PRODUCT FROM INVOICE
            $invoice_products = $refund->refundedProducts;
            // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
            foreach($invoice_products as $product) {
                // CHECK IF REFUND FROM CUSTOMER TO COMPANY
                if ($refund->type == 'in') {
                    // CHECK WEATHER THE PRODUCT IS VALID TO INCREASE QUANTITY OR NOT
                    if ($product->valid == '1') {
                        $this->updateProductActualQuantity('adding', $product);
                    }
                }
                // IF REFUND TO SUPPLIER THEN CALCULATIONS WILL BE WITHIN THE RefundProductObserver
            }
        }
    }

    /**
     * Handle the refund "deleted" event.
     *
     * @param Refund $refund
     * @return void
     */
    public function deleted(Refund $refund)
    {
        // RESTORE REFUNDED QUANTITY IF `type==='out'`
        if ($refund->type == 'out') {
            $invoice_products = $refund->refundedProducts;
            // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
            foreach($invoice_products as $product) {
                // CHECK WEATHER THE PRODUCT IS VALID TO INCREASE QUANTITY OR NOT
                $this->updateProductActualQuantity('adding', $product);
            }
        }
    }

    /**
     * Handle the refund "restored" event.
     *
     * @param Refund $refund
     * @return void
     */
    public function restored(Refund $refund)
    {
        //
    }

    /**
     * Handle the refund "force deleted" event.
     *
     * @param Refund $refund
     * @return void
     */
    public function forceDeleted(Refund $refund)
    {
        //
    }
}
