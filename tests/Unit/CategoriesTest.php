<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Category\Category;
use App\User;


class CategoriesTest extends TestCase
{
    use DatabaseTransactions;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'description'
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('categories.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_store_method () {
        $user = User::first();
        // NEW CATEGORY ASSOC ARRAY
        $new_category = [
            'name'          => str_random(20),
            'description'   => 'Nullable anyway',
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => ''
                ]
            ],
        ];


        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('categories.store'), $new_category);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_category['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_category = Category::create([
            'name'          => 'testing edit',
            'description'   => 'Nullable anyway',
            'created_by'    => $user -> id,
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => '',
                    'created_by'    => $user -> id,
                ]
            ],
        ]);

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('categories.edit', $new_category -> id));
        $response -> assertStatus(200);

        $response -> assertJsonStructure([
            'id', 'name', 'description', 'subcategories'
        ]);
    }

    public function test_update_method () {
        $user = User::first();
        $new_category = Category::create([
            'name'          => str_random(20),
            'description'   => 'Nullable anyway',
            'created_by'    => $user -> id,
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => '',
                    'created_by'    => $user -> id,
                ]
            ],
        ]);

        $new_category_updates = [
            'name'          => 'testing category',
            'description'   => 'Nullable anyway',
            'updated_by'    => $user -> id,
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => '',
                    'updated_by'    => $user -> id,
                ]
            ],
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('PATCH', route('categories.update', $new_category -> id), $new_category_updates);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_category_updates['name']);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE NEW
        $new_category = Category::create([
            'name'          => 'Category search',
            'description'   => 'Its nullable anyway.',
            'created_by'    => $user -> id,
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => '',
                    'created_by'    => $user -> id,
                ]
            ],
        ]);

        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('categories.search'), ["query"=>$new_category -> name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_category -> name);
    }

    public function test_delete_method () {
        // NEW DATA
        $new_category = [
            'name'          => str_random(20),
            'description'   => 'Nullable anyway',
            'subcategories' => [
                [
                    'name' => str_random(20),
                    'description' => '',
                ]
            ],
        ];

        // INSERT NEW
        $user = User::first();
        $new_category_add_response = $this -> actingAs($user, 'api') -> json('POST', route('categories.store'), $new_category);
        $new_category_add_response  -> assertStatus(200);
        $new_category_add_content = json_decode($new_category_add_response ->getContent());

        // DELETE
        $new_category_delete_response = $this -> actingAs($user, 'api') -> json('DELETE', route('categories.delete', $new_category_add_content -> id));
        $new_category_delete_response -> assertStatus(200);
        $new_category_delete_content = json_decode($new_category_delete_response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($new_category_delete_content -> deleted_at);

        // CHECK IF THE NEW DELETED CATEGORY DOESN'T APPEAR IN ELOQUENT
        $exists = Category::find($new_category_add_content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT NEW
        $new_category = Category::create([
            'name'          => 'category restore',
            'description'   => 'Nullable anyway',
            'created_by'    => $user -> id
        ]);

        // DELETE
        $new_category -> delete();

        // CHECK IF DELETED
        $exists = Category::find($new_category -> id);
        $this -> assertNull($exists);

        // RESTORING
        $response = $this -> actingAs($user, 'api') -> json('GET', route('categories.restore', $new_category -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = Category::find($new_category -> id);
        $this -> assertNotNull($exists);
    }
}
