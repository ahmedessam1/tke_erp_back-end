<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Warehouse;
use App\User;

class WarehousesTest extends TestCase
{
    use DatabaseTransactions;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'description', 'location'
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('warehouses.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_store_method () {
        $user = User::first();
        // NEW WAREHOUSE ASSOC ARRAY
        $new_warehouse = [
            'name'          => 'testing warehouses',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes'
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('warehouses.store'), $new_warehouse);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_warehouse['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_warehouse = Warehouse::create([
            'name'          => 'testing edit',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes!',
            'created_by'    => $user -> id
        ]);

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('warehouses.edit', $new_warehouse -> id));
        $response -> assertStatus(200);

        $response -> assertJsonStructure([
            'id', 'name', 'description', 'location'
        ]);
    }

    public function test_update_method () {
        $user = User::first();
        $new_warehouse = Warehouse::create([
            'name'          => 'testing update',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes!',
            'created_by'    => $user -> id
        ]);

        $new_warehouse_updates = [
            'name'          => 'testing warehouse',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes!',
            'updated_by'    => $user -> id
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('PATCH', route('warehouses.update', $new_warehouse -> id), $new_warehouse_updates);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_warehouse_updates['name']);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE A NEW WAREHOUSE
        $new_warehouse = Warehouse::create([
            'name'          => 'warehouse search',
            'description'   => 'Its nullable anyway.',
            'location'      => 'Whatever it takes..',
            'created_by'    => $user -> id
        ]);

        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('warehouses.search'), ["query"=>$new_warehouse->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_warehouse -> name);
    }

    public function test_delete_method () {
        // NEW WAREHOUSE DATA
        $new_warehouse = [
            'name'          => 'warehouse delete',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes!',
        ];

        // INSERT THE NEW WAREHOUSE
        $user = User::first();
        $new_warehouse_add_response = $this -> actingAs($user, 'api') -> json('POST', route('warehouses.store'), $new_warehouse);
        $new_warehouse_add_response  -> assertStatus(200);
        $new_warehouse_add_content = json_decode($new_warehouse_add_response ->getContent());

        // DELETE THE WAREHOUSE
        $new_warehouse_delete_response = $this -> actingAs($user, 'api') -> json('DELETE', route('warehouses.delete', $new_warehouse_add_content -> id));
        $new_warehouse_delete_response -> assertStatus(200);
        $new_warehouse_delete_content = json_decode($new_warehouse_delete_response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($new_warehouse_delete_content -> deleted_at);

        // CHECK IF THE NEW DELETED WAREHOUSE DOESN'T APPEAR IN ELOQUENT
        $exists = Warehouse::find($new_warehouse_add_content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT THE NEW WAREHOUSE
        $new_warehouse = Warehouse::create([
            'name'          => 'Warehouse restore',
            'description'   => 'Nullable anyway',
            'location'      => 'Whatever it takes!',
            'created_by'    => $user -> id
        ]);

        // DELETE THE WAREHOUSE
        $new_warehouse -> delete();

        // CHECK IF DELETED
        $exists = Warehouse::find($new_warehouse -> id);
        $this -> assertNull($exists);

        // RESTORING THE NEW DELETED WAREHOUSE
        $response = $this -> actingAs($user, 'api') -> json('GET', route('warehouses.restore', $new_warehouse -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = Warehouse::find($new_warehouse -> id);
        $this -> assertNotNull($exists);
    }
}
