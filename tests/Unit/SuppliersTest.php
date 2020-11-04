<?php

namespace Tests\Unit;

use App\Models\Position;
use App\Models\Supplier\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;


class SuppliersTest extends TestCase
{
    use DatabaseTransactions;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'description', 'created_by', 'updated_by',
                    'addresses' => [
                        '*' => [
                            'address',
                            'contacts' => [
                                '*' => [
                                    'name', 'phone_number'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    protected function response_structure_single_check ($response) {
        $response -> assertJsonStructure([
            'id', 'name', 'description', 'created_by', 'updated_by',
            'addresses' => [
                '*' => [
                    'address',
                    'contacts' => [
                        '*' => [
                            'name', 'phone_number'
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function create_new () {
        $user = User::first();
        $position_id = Position::first() -> id;
        return Supplier::create([
            'name'          => str_random(10),
            'description'   => 'test description',
            'address_contact_inputs' => [
                [
                    'address' => 'test address',
                    'contacts' => [
                        [
                            'position_id' => $position_id,
                            'name' => 'test name',
                            'phone_number' => '01112644917',
                            'created_by'    => $user -> id,
                        ]
                    ],
                    'created_by'    => $user -> id,
                ]
            ],
            'created_by'    => $user -> id,
        ]);
    }



    /******************************************
     * ***********TESTING FUNCTIONS************
     * ****************************************
     */


    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('suppliers.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_show_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $new_supplier = $this -> create_new();
        $response = $this -> actingAs($user, 'api') -> get(route('suppliers.show', $new_supplier -> id));
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        $this -> assertEquals($content -> name, $new_supplier['name']);
    }

    public function test_store_method () {
        $user = User::first();
        $position_id = Position::first() -> id;
        // NEW SUPPLIER ASSOC ARRAY
        $new_supplier = [
            'name'          => str_random(30),
            'description'   => 'test description',
            'address_contact_inputs' => [
                [
                    'address' => str_random(30),
                    'contacts' => [
                        [
                            'position_id' => $position_id,
                            'name' => str_random(20),
                            'phone_number' => '011'.rand(1, 9).'64491'.rand(1, 9),
                        ]
                    ]
                ]
            ]
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('suppliers.store'), $new_supplier);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_supplier['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_supplier = $this -> create_new();

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('suppliers.edit', $new_supplier -> id));
        $response -> assertStatus(200);

        $this -> response_structure_single_check($response);
    }

    public function test_update_method () {
        $user = User::first();
        $new_supplier = $this -> create_new();
        $position_id = Position::first() -> id;
        $new_supplier_updates = [
            'name'          => 'update supplier',
            'description'   => 'test description',
            'address_contact_inputs' => [
                [
                    'address' => 'test address',
                    'contacts' => [
                        [
                            'position_id' => $position_id,
                            'name' => 'test name',
                            'phone_number' => '01112644917',
                            'updated_by'    => $user -> id,
                        ]
                    ],
                    'updated_by'    => $user -> id,
                ]
            ],
            'updated_by'    => $user -> id,
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('PATCH', route('suppliers.update', $new_supplier -> id), $new_supplier_updates);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_supplier_updates['name']);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE A NEW SUPPLIER
        $new_supplier = $this -> create_new();


        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('suppliers.search'), ["query"=>$new_supplier->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_supplier -> name);
    }

    public function test_delete_method () {
        $user = User::first();
        $position_id = Position::first() -> id;
        // NEW SUPPLIER DATA
        $new_supplier = [
            'name'          => 'new supplier delete',
            'description'   => 'test description',
            'address_contact_inputs' => [
                [
                    'address' => 'test address',
                    'contacts' => [
                        [
                            'position_id' => $position_id,
                            'name' => 'test name',
                            'phone_number' => '01112644917',
                            'updated_by'    => $user -> id,
                        ]
                    ],
                    'updated_by'    => $user -> id,
                ]
            ],
            'updated_by'    => $user -> id,
        ];

        // INSERT THE NEW SUPPLIER
        $user = User::first();
        $new_supplier_add_response = $this -> actingAs($user, 'api') -> json('POST', route('suppliers.store'), $new_supplier);
        $new_supplier_add_response  -> assertStatus(200);
        $new_supplier_add_content = json_decode($new_supplier_add_response ->getContent());

        // DELETE THE SUPPLIER
        $new_supplier_delete_response = $this -> actingAs($user, 'api') -> json('DELETE', route('suppliers.delete', $new_supplier_add_content -> id));
        $new_supplier_delete_response -> assertStatus(200);
        $new_supplier_delete_content = json_decode($new_supplier_delete_response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($new_supplier_delete_content -> deleted_at);

        // CHECK IF THE NEW DELETED SUPPLIER DOESN'T APPEAR IN ELOQUENT
        $exists = Supplier::find($new_supplier_delete_content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT THE NEW SUPPLIER
        $new_supplier = $this -> create_new();

        // DELETE THE SUPPLIER
        $new_supplier -> delete();

        // CHECK IF DELETED
        $exists = Supplier::find($new_supplier -> id);
        $this -> assertNull($exists);

        // RESTORING THE NEW DELETED SUPPLIER
        $response = $this -> actingAs($user, 'api') -> json('GET', route('suppliers.restore', $new_supplier -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = Supplier::find($new_supplier -> id);
        $this -> assertNotNull($exists);
    }

}
