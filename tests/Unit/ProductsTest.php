<?php

namespace Tests\Unit;

use App\Models\Category\Category;
use App\Models\Category\Subcategory;
use App\Models\Product\ProductImages;
use App\Models\Season;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Traits\Logic\GenerateLocalCode;
use Illuminate\Http\UploadedFile;
use App\Models\Product\LocalCode;
use App\Models\Product\Product;
use Tests\TestCase;
use App\User;
use Storage;

class ProductsTest extends TestCase
{
    use DatabaseTransactions, GenerateLocalCode;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'barcode', 'seasons', 'category', 'images', 'description', 'created_by'
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    protected function response_structure_single_check ($response) {
        $response -> assertJsonStructure([
            'id', 'name', 'barcode', 'seasons', 'category', 'images', 'description', 'created_by'
        ]);
    }

    public function create_new () {
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



    /******************************************
     * ***********TESTING FUNCTIONS************
     * ****************************************
     */

    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('products.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_show_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $new_product = $this -> create_new();
        $response = $this -> actingAs($user, 'api') -> get(route('products.show', $new_product -> id));
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());
        $this -> assertEquals($content -> name, $new_product['name']);
    }

    public function test_store_method () {
        $user = User::first();
        $local_code_id = LocalCode::first() -> id;
        $seasons = Season::first();
        // category and subcategories
        $category = Category::orderBy('id', 'DESC') -> first();
        $subcategories_id = [];
        foreach ($category -> subcategories as $subcategories)
            array_push($subcategories_id, $subcategories -> id);

        // NEW PRODUCT ASSOC ARRAY
        Storage::fake('public');
        $new_product = [
            'name'          => 'testing product',
            'barcode'       => '1231231321231',
            'category_id'   => $category -> id,
            'subcategories_id' => $subcategories_id,
            'local_code_id' => $local_code_id,
            'seasons_id'    => [$seasons -> id],
            'description'   => 'test description',
            'created_by'    => $user -> id,
            'image'         => UploadedFile::fake() -> image('avatar.jpg')
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('products.store'), $new_product);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_product['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_product = $this -> create_new();

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('products.edit', $new_product -> id));
        $response -> assertStatus(200);

        $this -> response_structure_single_check($response);
    }

    public function test_update_method () {
        $user = User::first();
        $new_product = $this -> create_new();

        $local_code_id = LocalCode::first() -> id;

        // ADDING TWO SEASONS
        $season_one = Season::create([
            'name'          => str_random(20),
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by' => 1,
        ]);
        $season_two = Season::create([
            'name'          => str_random(20),
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by' => 1,
        ]);
        // category and subcategories
        $category = Category::orderBy('id', 'DESC') -> first();
        $subcategories_id = [];
        foreach ($category -> subcategories as $subcategories)
            array_push($subcategories_id, $subcategories -> id);
        // NEW PRODUCT ASSOC ARRAY
        $new_product_updates = [
            '_method'       => 'PATCH',
            'name'          => str_random(10),
            'barcode'       => '1231231321888',
            'category_id'   => $category -> id,
            'subcategories_id' => $subcategories_id,
            'local_code_id' => $local_code_id,
            'seasons_id'    => [$season_one -> id, $season_two -> id],
            'description'   => 'test description',
            'updated_by'    => $user -> id,
        ];


        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('products.update', $new_product -> id), $new_product_updates);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_product_updates['name']);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE A NEW PRODUCT
        $new_product = $this -> create_new();


        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('products.search'), ["query"=>$new_product->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_product -> name);
    }

    public function test_delete_method () {
        $user = User::first();
        $local_code_id = LocalCode::first() -> id;
        $seasons = Season::first();
        // category and subcategories
        $category = Category::orderBy('id', 'DESC') -> first();
        $subcategories_id = [];
        foreach ($category -> subcategories as $subcategories)
            array_push($subcategories_id, $subcategories -> id);
        // NEW PRODUCT ASSOC ARRAY
        Storage::fake('public');
        $new_product = [
            'name'          => 'testing product',
            'barcode'       => '1231231321231',
            'category_id'   => $category -> id,
            'subcategories_id' => $subcategories_id,
            'local_code_id' => $local_code_id,
            'seasons_id'    => [$seasons -> id],
            'description'   => 'test description',
            'created_by'    => $user -> id,
            'image'         => UploadedFile::fake() -> image('avatar.jpg')
        ];

        // INSERT THE NEW PRODUCT
        $new_product_add_response = $this -> actingAs($user, 'api') -> json('POST', route('products.store'), $new_product);
        $new_product_add_response  -> assertStatus(200);
        $new_product_add_content = json_decode($new_product_add_response ->getContent());

        // DELETE THE PRODUCT
        $new_product_delete_response = $this -> actingAs($user, 'api') -> json('DELETE', route('products.delete', $new_product_add_content -> id));
        $new_product_delete_response -> assertStatus(200);
        $new_product_delete_content = json_decode($new_product_delete_response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($new_product_delete_content -> deleted_at);

        // CHECK IF THE NEW DELETED SUPPLIER DOESN'T APPEAR IN ELOQUENT
        $exists = Product::find($new_product_delete_content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT THE NEW PRODUCT
        $new_product = $this -> create_new();

        // DELETE THE PRODUCT
        $new_product -> delete();

        // CHECK IF DELETED
        $exists = Product::find($new_product -> id);
        $this -> assertNull($exists);

        // RESTORING THE NEW DELETED PRODUCT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('products.restore', $new_product -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = Product::find($new_product -> id);
        $this -> assertNotNull($exists);
    }

    public function test_adding_images_to_existing_product () {
        $user = User::first();
        $new_product = $this -> create_new();

        $product_id = $new_product -> id;

        // NEW PRODUCT ASSOC ARRAY
        Storage::fake('public');
        $new_image_info = [
            'product_id'  => $product_id,
            'file'        => UploadedFile::fake() -> image('avatar1.jpg')
        ];

        // UPLOAD IMAGES TO FIRST PRODUCT
        $new_images = $this -> actingAs($user, 'api') -> json('POST', route('products.add.image'), $new_image_info);
        $new_images_content = json_decode($new_images -> getContent());

        // CHECK IF FILE EXISTS IN STORAGE AND DATABASE
        Storage::disk('local')->assertExists('public/uploads/products/main/'.$new_images_content -> large_image);
        Storage::disk('local')->assertExists('public/uploads/products/thumbnail/'.$new_images_content -> thumbnail_image);
    }

    public function test_removing_images_form_existing_product () {
        $user = User::first();
        $product = $this -> create_new();
        $product_id = $product -> id;

        // NEW PRODUCT ASSOC ARRAY
        Storage::fake('public');
        $new_image_info = [
            'product_id'  => $product_id,
            'file'        => UploadedFile::fake() -> image('avatar1.jpg')
        ];

        // UPLOAD IMAGES TO FIRST PRODUCT
        $new_images = $this -> actingAs($user, 'api') -> json('POST', route('products.add.image'), $new_image_info);
        $new_images_content = json_decode($new_images -> getContent());

        // REMOVE ADDED IMAGE
        $this -> actingAs($user, 'api') -> json('GET', route('products.remove.image', [$product_id, $new_images_content -> id]));

        // CHECK IN DATABASE
        $check_db = ProductImages::where('product_id', $product_id) -> where('id', $new_images_content -> id) -> count();
        $this -> assertEquals($check_db, 0);

        // CHECK IF FILE EXISTS
        Storage::disk('local')->assertMissing('public/uploads/products/main/'.$new_images_content -> large_image);
        Storage::disk('local')->assertMissing('public/uploads/products/thumbnail/'.$new_images_content -> thumbnail_image);
    }
}
