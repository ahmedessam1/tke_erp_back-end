<?php

namespace App\Observers\Invoice;

use App\Models\Invoices\ImportInvoice;
use App\Traits\Observers\Helpers\ProductsCalculations;

class ImportInvoiceObserver
{
    use ProductsCalculations;
    /**
     * Handle the import invoice "created" event.
     *
     * @param ImportInvoice $importInvoice
     * @return void
     */
    public function created(ImportInvoice $importInvoice)
    {
        //
    }

    /**
     * Handle the import invoice "updated" event.
     *
     * @param ImportInvoice $import_invoice
     * @return void
     */
    public function updated(ImportInvoice $import_invoice)
    {
        // UPDATING PRODUCTS QUANTITY AND AVERAGE PURCHASE PRICE IF APPROVED
        if ($import_invoice->approve == '1') {
            // GET PRODUCT FROM INVOICE
            $invoice_products = $import_invoice->productCredits;
            // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
            foreach($invoice_products as $product) {
                // FIRST CALCULATE THE AVERAGE PURCHASE PRICE THEN ADD THE QUANTITY BECAUSE
                // IF QUANTITY IS ADDED FIRST THEN IT WILL AFFECT THE AVERAGE PURCHASE PRICE
                $this->updateProductAveragePurchasePrice($product);
                $this->updateProductActualQuantity('adding', $product);
            }
        }
    }

    /**
     * Handle the import invoice "deleted" event.
     *
     * @param ImportInvoice $importInvoice
     * @return void
     */
    public function deleted(ImportInvoice $importInvoice)
    {
        //
    }

    /**
     * Handle the import invoice "restored" event.
     *
     * @param ImportInvoice $importInvoice
     * @return void
     */
    public function restored(ImportInvoice $importInvoice)
    {
        //
    }

    /**
     * Handle the import invoice "force deleted" event.
     *
     * @param ImportInvoice $importInvoice
     * @return void
     */
    public function forceDeleted(ImportInvoice $importInvoice)
    {
        //
    }
}
