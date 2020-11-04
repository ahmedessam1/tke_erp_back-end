<?php

namespace App\Observers\Invoice;

use App\Models\Invoices\ExportInvoice;
use App\Traits\Observers\Helpers\ProductsCalculations;

class ExportInvoiceObserver
{
    use ProductsCalculations;
    /**
     * Handle the export invoice "created" event.
     *
     * @param ExportInvoice $exportInvoice
     * @return void
     */
    public function created(ExportInvoice $exportInvoice)
    {
        //
    }

    /**
     * Handle the export invoice "updated" event.
     *
     * @param ExportInvoice $export_invoice
     * @return void
     */
    public function updated(ExportInvoice $export_invoice)
    {
        // UPDATING PRODUCT AVERAGE SELLING PRICE IF APPROVED
        if ($export_invoice->approve == '1') {
            // GET PRODUCT FROM INVOICE
            $invoice_products = $export_invoice->soldProducts;
            // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
            foreach($invoice_products as $product)
                $this->updateProductAverageSellPrice($product);
        }
    }

    /**
     * Handle the export invoice "deleted" event.
     *
     * @param ExportInvoice $export_invoice
     * @return void
     */
    public function deleted(ExportInvoice $export_invoice)
    {
        // GET PRODUCT FROM INVOICE
        $invoice_products = $export_invoice->soldProducts;
        // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
        foreach($invoice_products as $product)
            $this->updateProductActualQuantity('adding', $product);
    }

    /**
     * Handle the export invoice "restored" event.
     *
     * @param ExportInvoice $exportInvoice
     * @return void
     */
    public function restored(ExportInvoice $exportInvoice)
    {
        //
    }

    /**
     * Handle the export invoice "force deleted" event.
     *
     * @param ExportInvoice $exportInvoice
     * @return void
     */
    public function forceDeleted(ExportInvoice $exportInvoice)
    {
        //
    }
}
