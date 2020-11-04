<?php

namespace Tests\Unit;

use App\Models\Category\Category;
use App\Models\Customer\CustomerBranch;
use App\Models\Invoices\ExportInvoice;
use App\Models\Product\LocalCode;
use App\Models\Product\Product;
use App\Models\Product\SoldProducts;
use App\Models\Season;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Storage;

class ExportInvoicesCalculationsTest extends TestCase
{
    use DatabaseTransactions;

    // CREATING NEW PRODUCT
    private function insertProduct () {
        $user = User::first();

        $local_code_id = LocalCode::first() -> id;
        $seasons = Season::first();
        // category and subcategories
        $category = Category::orderBy('id', 'DESC') -> first();
        $subcategories_id = [];
        foreach ($category -> subcategories as $subcategories)
            array_push($subcategories_id, $subcategories -> id);

        // CREATE PRODUCT
        Storage::fake('public');
        return Product::create([
            'name'          => str_random(15),
            'barcode'       => '12312'.rand(100, 999).'21238',
            'category_id'   => $category -> id,
            'subcategories_id' => $subcategories_id,
            'local_code_id' => $local_code_id,
            'seasons_id'    => [$seasons -> id],
            'description'   => 'test description',
            'created_by'    => $user -> id,
            'image'         => UploadedFile::fake() -> image('avatar.jpg')
        ]);
    }


    // CREATING NEW IMPORT INVOICE
    private function insertInvoice ($tax, $discount, $approve, $products_quantity, $products_sold_price, $products_discount) {
        $user = User::first() -> id;
        $customer_branch_id = CustomerBranch::first() -> id;

        $export_invoice = ExportInvoice::create([
            'seller_id' => $user,
            'name' => str_random(20),
            'number' => rand(50, 9999),
            'customer_branch_id' => $customer_branch_id,
            'tax' => $tax,
            'discount' => $discount,
            'date' => '2019-08-26',
            'approve' => $approve,
            'created_by'  => $user,
        ]);

        $product_one = $this -> insertProduct();
        $product_two = $this -> insertProduct();

        SoldProducts::create([
            'product_id'        => $product_one -> id,
            'export_invoice_id' => $export_invoice -> id,
            'quantity'          => $products_quantity[0],
            'sold_price'        => $products_sold_price[0],
            'discount'          => $products_discount[0],
            'created_by'        => $user,
        ]);

        SoldProducts::create([
            'product_id'        => $product_two -> id,
            'export_invoice_id' => $export_invoice -> id,
            'quantity'          => $products_quantity[1],
            'sold_price'        => $products_sold_price[1],
            'discount'          => $products_discount[1],
            'created_by'        => $user,
        ]);

        return $export_invoice;
    }

    private function invoiceTotal ($invoice) {
        $total = 0;
        $products = SoldProducts::where('export_invoice_id', $invoice -> id) -> get();
        // CALCULATE THE PRODUCT TOTAL AND CHECKING DISCOUNT
        foreach($products as $product) {
            // CHECK PRODUCT DISCOUNT
            $total_purchase_price = $product -> sold_price * $product -> quantity;
            $product_discount_amount = 0;
            if ($product -> discount > 0) {
                $product_discount_amount = $total_purchase_price * $product -> discount / 100;
            }
            $total += ($total_purchase_price - $product_discount_amount);
        }
        // ADDING INVOICE DISCOUNT
        $invoice_discount_amount = 0;
        if ($invoice -> discount > 0)
            $invoice_discount_amount = $total * $invoice -> discount / 100;
        $total -= $invoice_discount_amount;

        // ADDING INVOICE TAX
        if ($invoice -> tax > 0)
            $total *= 1.14;
        return $total;
    }



    // TESTING FUNCTIONS
    public function testExportInvoiceTotal()
    {
        $user = User::first();
        /*
         * 1) WITH TAX
         * 2) WITHOUT DISCOUNT
         * 3) UNAPPROVED
         * 4) PRODUCTS QUANTITIES: [10, 20]
         * 5) PRODUCTS PURCHASE PRICE: [100, 200]
         * 6) PRODUCTS DISCOUNTS: [0, 0]
         */
        $invoice = $this -> insertInvoice(1, 0, 0, [10, 20], [100, 200], [0, 0]);
        // GETTING THE CREATED INVOICE TOTAL
        $invoice_total = $this -> invoiceTotal($invoice);
        // CALLING IMPORT INVOICE SHOW ROUTE
        $response = $this -> actingAs($user, 'api') -> get(route('export_invoices.show', $invoice -> id));
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // ASSERT TOTAL MATCHES
        $this -> assertEquals($invoice_total, $content -> invoice_total);

        // ASSERT PRODUCT QUANTITY MATCHES
        $this -> assertEquals($content -> export_invoice -> sold_products[0] -> quantity, 10);
        $this -> assertEquals($content -> export_invoice -> sold_products[1] -> quantity, 20);
    }
}
