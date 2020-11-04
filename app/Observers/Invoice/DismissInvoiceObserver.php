<?php

namespace App\Observers\Invoice;

use App\Models\ProductDismissOrder\ProductDismissOrder;
use App\Traits\Observers\Helpers\ProductsCalculations;

class DismissInvoiceObserver
{
    use ProductsCalculations;
    /**
     * Handle the product dismiss order "created" event.
     *
     * @param ProductDismissOrder $productDismissOrder
     * @return void
     */
    public function created(ProductDismissOrder $productDismissOrder)
    {
        //
    }

    /**
     * Handle the product dismiss order "updated" event.
     *
     * @param ProductDismissOrder $productDismissOrder
     * @return void
     */
    public function updated(ProductDismissOrder $productDismissOrder)
    {
        //
    }

    /**
     * Handle the product dismiss order "deleted" event.
     *
     * @param ProductDismissOrder $productDismissOrder
     * @return void
     */
    public function deleted(ProductDismissOrder $productDismissOrder)
    {
        // GET PRODUCT FROM INVOICE
        $invoice_products = $productDismissOrder->productDismissOrderProducts;
        // ADD QUANTITIES TO PRODUCTS USING ProductsCalculations Trait
        foreach($invoice_products as $product)
            $this->updateProductActualQuantity('adding', $product);
    }

    /**
     * Handle the product dismiss order "restored" event.
     *
     * @param ProductDismissOrder $productDismissOrder
     * @return void
     */
    public function restored(ProductDismissOrder $productDismissOrder)
    {
        //
    }

    /**
     * Handle the product dismiss order "force deleted" event.
     *
     * @param ProductDismissOrder $productDismissOrder
     * @return void
     */
    public function forceDeleted(ProductDismissOrder $productDismissOrder)
    {
        //
    }
}
