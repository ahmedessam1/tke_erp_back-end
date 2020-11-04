<?php

namespace Tests\Unit;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Product\ProductCredits;
use App\Models\Invoices\ImportInvoice;
use App\Models\Supplier\Supplier;
use App\Models\Product\Product;
use Tests\TestCase;
use App\User;

class ImportInvoicesTest extends TestCase
{
    use DatabaseTransactions;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'number', 'name', 'date', 'tax', 'discount', 'supplier', 'created_by'
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    protected function response_structure_single_check ($response) {
        $response -> assertJsonStructure([
            'id', 'number', 'name', 'date', 'tax', 'discount', 'supplier_id', 'created_by',
            'product_credits' => [
                '*' => [
                    'product_id', 'package_size', 'quantity', 'purchase_price', 'discount'
                ]
            ],
        ]);
    }

    public function create_new () {
        $user        = User::first();
        $supplier_id = Supplier::first() -> id;
        $product_id  = Product::first() -> id;

        // CREATE INVOICE BASIC INFO
        $import_invoice = ImportInvoice::create([
            'name'          => str_random(15),
            'number'        => rand(100, 999),
            'supplier_id'   => $supplier_id,
            'date'          => '2018-12-04',
            'tax'           => rand(0, 1),
            'discount'      => rand(0, 5),
            'created_by'    => $user -> id,
        ]);

        ProductCredits::create([
            'product_id'        => $product_id,
            'import_invoice_id' => $import_invoice -> id,
            'quantity'          => rand(4, 15),
            'package_size'      => rand(4, 8),
            'purchase_price'    => rand(20, 100),
            'discount'          => rand(0, 10),
            'created_by'    => $user -> id,
        ]);

        // RETURN IMPORT INVOICE
        return $import_invoice;
    }



    /******************************************
     * ***********TESTING FUNCTIONS************
     * ****************************************
     */

    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('import_invoices.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_show_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $new_import_invoice = $this -> create_new();
        $response = $this -> actingAs($user, 'api') -> get(route('import_invoices.show', $new_import_invoice -> id));
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        $this -> assertEquals($content -> import_invoice -> name, $new_import_invoice['name']);
    }

    public function test_store_method () {
        $user = User::first();
        $supplier_id  = Supplier::first() -> id;
        $product_id   = Product::first() -> id;
        $warehouse_id = Warehouse::first() -> id;
        // CREATE INVOICE BASIC INFO
        $import_invoice = [
            'name'          => str_random(15),
            'number'        => rand(100, 999),
            'supplier_id'   => $supplier_id,
            'date'          => '2018-12-04',
            'tax'           => rand(0, 1),
            'discount'      => rand(0, 5),
        ];


        $products_credit = [
            'product_id'        => $product_id,
            'quantity'          => rand(4, 15),
            'package_size'      => rand(4, 8),
            'purchase_price'    => rand(20, 100),
            'discount'          => rand(0, 10),
            'warehouse_id'      => $warehouse_id,
        ];

        $new_import_invoice_data = ['invoice_data' => $import_invoice, 'products_credit' => [$products_credit]];


        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('import_invoices.store'), $new_import_invoice_data);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $import_invoice['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_import_invoice = $this -> create_new();

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('import_invoices.edit', $new_import_invoice -> id));
        $response -> assertStatus(200);

        $this -> response_structure_single_check($response);
    }

    public function test_update_method () {
        $user = User::first();
        $supplier_id = Supplier::first() -> id;
        $product_id  = Product::first() -> id;
        $warehouse_id  = Warehouse::first() -> id;
        $new_import_invoice = $this -> create_new();

        // CREATE INVOICE BASIC INFO
        $new_import_invoice_updates = [
            'name'          => str_random(15),
            'number'        => rand(100, 999),
            'supplier_id'   => $supplier_id,
            'date'          => '2018-12-04',
            'tax'           => rand(0, 1),
            'discount'      => rand(0, 5),
            'updated_by'    => $user -> id,
        ];

        $products_credit = [
            'product_id'        => $product_id,
            'import_invoice_id' => $new_import_invoice -> id,
            'quantity'          => rand(4, 15),
            'package_size'      => rand(4, 8),
            'purchase_price'    => rand(20, 100),
            'warehouse_id'      => $warehouse_id,
            'discount'          => rand(0, 10),
            'updated_by'        => $user -> id,
        ];

        $new_import_invoice_data = [
            '_method'           => 'PATCH',
            'invoice_data'      => $new_import_invoice_updates,
            'products_credit'   => [$products_credit],
        ];


        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('import_invoices.update', $new_import_invoice -> id), $new_import_invoice_data);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> updated_by, $user -> id);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE A NEW IMPORT INVOICE
        $new_import_invoice = $this -> create_new();

        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('import_invoices.search'), ["query" => $new_import_invoice->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_import_invoice -> name);
    }

    public function test_delete_method () {
        $user = User::first();
        $new_import_invoice = $this -> create_new();

        // DELETE THE IMPORT INVOICE
        $response = $this -> actingAs($user, 'api') -> json('DELETE', route('import_invoices.delete', $new_import_invoice -> id));
        $response -> assertStatus(200);
        $content = json_decode($response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($content -> deleted_at);

        // CHECK IF THE NEW DELETED SUPPLIER DOESN'T APPEAR IN ELOQUENT
        $exists = Product::find($content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT THE NEW IMPORT INVOICE
        $new_import_invoice = $this -> create_new();

        // DELETE THE IMPORT INVOICE
        $new_import_invoice -> delete();

        // CHECK IF DELETED
        $exists = ImportInvoice::find($new_import_invoice -> id);
        $this -> assertNull($exists);

        // RESTORING THE NEW DELETED IMPORT INVOICE
        $response = $this -> actingAs($user, 'api') -> json('GET', route('import_invoices.restore', $new_import_invoice -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = ImportInvoice::find($new_import_invoice -> id);
        $this -> assertNotNull($exists);
    }
}
