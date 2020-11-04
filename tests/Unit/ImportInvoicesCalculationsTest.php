<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Invoices\ImportInvoice;
use App\Models\Product\ProductCredits;
use App\Models\Category\Category;
use Illuminate\Http\UploadedFile;
use App\Models\Supplier\Supplier;
use App\Models\Product\LocalCode;
use App\Models\Product\Product;
use App\Models\Season;
use Tests\TestCase;
use App\User;
use Storage;

class ImportInvoicesCalculationsTest extends TestCase
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
    private function insertInvoice ($tax, $discount, $approve, $products_quantity, $products_purchase_price, $products_discount) {
        $user = User::first() -> id;
        $supplier_id = Supplier::first() -> id;

        $import_invoice = ImportInvoice::create([
            'supplier_id' => $supplier_id,
            'name' => str_random(20),
            'number' => rand(50, 9999),
            'date' => '2019-08-26',
            'tax' => $tax,
            'discount' => $discount,
            'approve' => $approve,
            'created_by'    => $user,
        ]);

        $product_one = $this -> insertProduct();
        $product_two = $this -> insertProduct();

        ProductCredits::create([
            'product_id'        => $product_one -> id,
            'import_invoice_id' => $import_invoice -> id,
            'quantity'          => $products_quantity[0],
            'package_size'      => rand(4, 8),
            'purchase_price'    => $products_purchase_price[0],
            'discount'          => $products_discount[0],
            'created_by'        => $user,
        ]);

        ProductCredits::create([
            'product_id'        => $product_two -> id,
            'import_invoice_id' => $import_invoice -> id,
            'quantity'          => $products_quantity[1],
            'package_size'      => rand(4, 8),
            'purchase_price'    => $products_purchase_price[1],
            'discount'          => $products_discount[1],
            'created_by'        => $user,
        ]);

        return $import_invoice;
    }

    private function invoiceTotal ($invoice) {
        $total = 0;
        $products = ProductCredits::where('import_invoice_id', $invoice -> id) -> get();
        // CALCULATE THE PRODUCT TOTAL AND CHECKING DISCOUNT
        foreach($products as $product) {
            // CHECK PRODUCT DISCOUNT
            $total_purchase_price = $product -> purchase_price * $product -> quantity;
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
    public function testImportInvoiceTotal()
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
        $response = $this -> actingAs($user, 'api') -> get(route('import_invoices.show', $invoice -> id));
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // ASSERT TOTAL MATCHES
        $this -> assertEquals($invoice_total, $content -> invoice_total);

        // ASSERT PRODUCT QUANTITY MATCHES
        $this -> assertEquals($content -> import_invoice -> product_credits[0] -> quantity, 10);
        $this -> assertEquals($content -> import_invoice -> product_credits[1] -> quantity, 20);
    }
}
