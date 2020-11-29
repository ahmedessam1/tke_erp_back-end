<?php

namespace App\Traits\Data;

use App\Models\Customer\CustomerPriceList;
use App\Models\Invoices\ExportInvoice;
use App\Models\Product\Product;

trait FilterProducts
{
    protected function categorySubcategoryProductsFiltering($category_id, $subcategories_id, $q, $type, $invoice_id)
    {
        if ($type !== 'selling') {
            return $this->getOriginalProductsInfo($category_id, $subcategories_id, $q);
        } else {
            return $this->getCustomerListProductsInfo($category_id, $subcategories_id, $q, $invoice_id);
        }
    }

    private function getOriginalProductsInfo($category_id, $subcategories_id, $q)
    {
        // SEARCH EXACTLY IF BARCODE INSERTED
        if (strlen($q) > 5 && is_numeric($q))
            $condition_queries = Product::where('barcode', 'LIKE', '%' . $q . '%');
        else
            $condition_queries = Product::search($q);

        if ($category_id != '')
            $condition_queries->where('category_id', $category_id);

        $collection = $condition_queries
            ->take(10)
            ->get();
        return $collection->load('category', 'subcategories', 'images');
    }

    private function getCustomerListProductsInfo($category_id, $subcategories_id, $q, $invoice_id)
    {
        // GET INVOICE AND CUSTOMER
        $invoice = ExportInvoice::withCustomerBranch()->find($invoice_id);
        $customer_id = $invoice->customerBranch->customer->id;

        // SEARCH EXACTLY IF BARCODE INSERTED
        if (strlen($q) > 5 && is_numeric($q))
            $condition_queries = Product::where('barcode', 'LIKE', '%' . $q . '%');
        else
            $condition_queries = Product::search($q);

        if ($category_id != '')
            $condition_queries->where('category_id', $category_id);

        $products = $condition_queries->take(10)->get()->load('category', 'subcategories', 'images');
        $products_ids = [];
        foreach($products as $p)
            array_push($products_ids, $p->id);

        // GET THE CUSTOMER PRODUCTS LIST INFO
        $customer_products_list = [];
        $products_from_customer_list = CustomerPriceList::where('customer_id', $customer_id)->whereIn('product_id', $products_ids)->get();

        foreach($products as $product) {
            foreach($products_from_customer_list as $p_f_l) {
                if ($product->id === $p_f_l->product_id) {
                    // ADDING PRODUCT NAME IF EXISTS
                    if ($p_f_l->product_name !== null)
                        $product->name = $p_f_l->product_name;

                    // ADDING PRODUCT BARCODE IF EXISTS
                    if ($p_f_l->product_barcode !== null)
                        $product->barcode = $p_f_l->product_barcode;

                    // ADDING PRODUCT SELLING PRICE IF EXISTS
                    if ($p_f_l->product_selling_price !== null)
                        $product->customer_selling_price = $p_f_l->product_selling_price;
                }
            }
        }
        return $products;
    }
}
