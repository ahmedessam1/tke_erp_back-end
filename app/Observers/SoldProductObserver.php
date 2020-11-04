<?php

namespace App\Observers;

use App\Models\Product\SoldProducts;
use App\Traits\Observers\Helpers\ProductsCalculations;
use App\Traits\Observers\InvoiceObserversTrait;

class SoldProductObserver
{
    use InvoiceObserversTrait, ProductsCalculations;

    /**
     * Handle the sold products "created" event.
     *
     * @param SoldProducts $sold_product
     * @return void
     */
    public function created(SoldProducts $sold_product)
    {
        $invoice = $sold_product->exportInvoice;

        // ADD PRODUCT NET PRICE TO INVOICE
        $this->calculateInvoiceTotalAndStore('create', 'export_invoice', $invoice, $sold_product);

        // REMOVING QUANTITIES FROM PRODUCT LOG USING ProductsCalculations Trait
        $this->updateProductActualQuantity('subtracting', $sold_product);
    }

    /**
     * Handle the sold products "updated" event.
     *
     * @param  \App\Models\Product\SoldProducts  $soldProducts
     * @return void
     */
    public function updated(SoldProducts $soldProducts)
    {
        //
    }

    /**
     * Handle the sold products "deleted" event.
     *
     * @param SoldProducts $sold_product
     * @return void
     */
    public function deleted(SoldProducts $sold_product)
    {
        $invoice = $sold_product->exportInvoice;
        // DELETING PRODUCT NET PRICE FROM INVOICE
        $this->calculateInvoiceTotalAndStore('delete', 'export_invoice', $invoice, $sold_product);

        // ADDING QUANTITIES TO PRODUCT LOG USING ProductsCalculations Trait
        $this->updateProductActualQuantity('adding', $sold_product);
    }

    /**
     * Handle the sold products "restored" event.
     *
     * @param  \App\Models\Product\SoldProducts  $soldProducts
     * @return void
     */
    public function restored(SoldProducts $soldProducts)
    {
        //
    }

    /**
     * Handle the sold products "force deleted" event.
     *
     * @param SoldProducts $sold_product
     * @return void
     */
    public function forceDeleted(SoldProducts $sold_product)
    {
        //
    }
}
