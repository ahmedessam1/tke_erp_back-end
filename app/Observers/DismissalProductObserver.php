<?php

namespace App\Observers;

use App\Models\ProductDismissOrder\ProductDismissOrderProducts;
use App\Traits\Observers\Helpers\ProductsCalculations;

class DismissalProductObserver
{
    use ProductsCalculations;

    /**
     * Handle the product dismiss order products "created" event.
     *
     * @param ProductDismissOrderProducts $product_dismiss_order_products
     * @return void
     */
    public function created(ProductDismissOrderProducts $product_dismiss_order_products)
    {
        // REMOVING QUANTITIES FROM PRODUCT LOG USING ProductsCalculations Trait
        $this->updateProductActualQuantity('subtracting', $product_dismiss_order_products);
    }

    /**
     * Handle the product dismiss order products "updated" event.
     *
     * @param ProductDismissOrderProducts $productDismissOrderProducts
     * @return void
     */
    public function updated(ProductDismissOrderProducts $productDismissOrderProducts)
    {
        //
    }

    /**
     * Handle the product dismiss order products "deleted" event.
     *
     * @param ProductDismissOrderProducts $product_dismiss_order_products
     * @return void
     */
    public function deleted(ProductDismissOrderProducts $product_dismiss_order_products)
    {
        //
    }

    /**
     * Handle the product dismiss order products "restored" event.
     *
     * @param ProductDismissOrderProducts $productDismissOrderProducts
     * @return void
     */
    public function restored(ProductDismissOrderProducts $productDismissOrderProducts)
    {
        //
    }

    /**
     * Handle the product dismiss order products "force deleted" event.
     *
     * @param ProductDismissOrderProducts $productDismissOrderProducts
     * @return void
     */
    public function forceDeleted(ProductDismissOrderProducts $productDismissOrderProducts)
    {
        //
    }
}
