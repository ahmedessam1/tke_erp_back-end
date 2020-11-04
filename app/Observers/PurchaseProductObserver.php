<?php

namespace App\Observers;

use App\Models\Product\ProductCredits;
use App\Traits\Observers\Helpers\ProductsCalculations;
use App\Traits\Observers\InvoiceObserversTrait;

class PurchaseProductObserver
{
    use InvoiceObserversTrait, ProductsCalculations;

    /**
     * Handle the product credits "created" event.
     *
     * @param ProductCredits $product_credits
     * @return void
     */

    public function created(ProductCredits $product_credits)
    {
        $invoice = $product_credits->importInvoice;
        // IF THERE IS NO INVOICE "INITIATORY CREDIT" THEN INCREASE PRODUCT QUANTITY DIRECT
        if (!$invoice) {
            $this->updateProductActualQuantity('adding', $product_credits);
            return;
        }
        // ADD PRODUCT NET PRICE TO INVOICE
        $this->calculateInvoiceTotalAndStore('create', 'import_invoice', $invoice, $product_credits);
    }

    /**
     * Handle the product credits "updated" event.
     *
     * @param  \App\Models\Product\ProductCredits  $productCredits
     * @return void
     */
    public function updated(ProductCredits $productCredits)
    {
        //
    }

    /**
     * Handle the product credits "deleted" event.
     *
     * @param  \App\Models\Product\ProductCredits  $productCredits
     * @return void
     */
    public function deleted(ProductCredits $productCredits)
    {
        $invoice = $productCredits->importInvoice;
        // IF THERE IS NO INVOICE "INITIATORY CREDIT" THEN DO NOTHING
        if (!$invoice)
            return;
        // REMOVE PRODUCT NET PRICE FROM INVOICE
        $this->calculateInvoiceTotalAndStore('delete','import_invoice', $invoice, $productCredits);
    }

    /**
     * Handle the product credits "restored" event.
     *
     * @param  \App\Models\Product\ProductCredits  $productCredits
     * @return void
     */
    public function restored(ProductCredits $productCredits)
    {
        //
    }

    /**
     * Handle the product credits "force deleted" event.
     *
     * @param  \App\Models\Product\ProductCredits  $productCredits
     * @return void
     */
    public function forceDeleted(ProductCredits $productCredits)
    {
        //
    }
}