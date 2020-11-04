<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Season;
use Tests\TestCase;
use App\User;

class SeasonsTest extends TestCase
{
    use DatabaseTransactions;

    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'starting_date', 'ending_date'
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    public function test_index_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('seasons.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_store_method () {
        $user = User::first();
        // NEW SEASON ASSOC ARRAY
        $new_season = [
            'name'          => str_random(20),
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28'
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('POST', route('seasons.store'), $new_season);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_season['name']);
    }

    public function test_edit_method () {
        $user = User::first();
        $new_season = Season::create([
            'name'          => 'testing edit',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by'    => $user -> id
        ]);

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('GET', route('seasons.edit', $new_season -> id));
        $response -> assertStatus(200);

        $response -> assertJsonStructure([
            'id', 'name', 'starting_date', 'ending_date'
        ]);
    }

    public function test_update_method () {
        $user = User::first();
        $new_season = Season::create([
            'name'          => 'testing update',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by'    => $user -> id
        ]);
        $new_season_updates = [
            'name'          => 'testing season',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'updated_by'    => $user -> id
        ];

        // CHECK IF RETURN 200 AND GET CONTENT
        $response = $this -> actingAs($user, 'api') -> json('PATCH', route('seasons.update', $new_season -> id), $new_season_updates);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> name, $new_season_updates['name']);
    }

    public function test_search_method () {
        $user = User::first();
        // CREATE A NEW SEASON
        $new_season = Season::create([
            'name'          => 'season search',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by'    => $user -> id
        ]);

        // CHECK IF RETURN 200
        $response = $this -> actingAs($user, 'api') -> json('GET', route('seasons.search'), ["query"=>$new_season->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> name, $new_season -> name);
    }

    public function test_delete_method () {
        // NEW SEASON DATA
        $new_season = [
            'name'          => 'season delete',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28'
        ];

        // INSERT THE NEW SEASON
        $user = User::first();
        $new_season_add_response = $this -> actingAs($user, 'api') -> json('POST', route('seasons.store'), $new_season);
        $new_season_add_response  -> assertStatus(200);
        $new_season_add_content = json_decode($new_season_add_response ->getContent());

        // DELETE THE SEASON
        $new_season_delete_response = $this -> actingAs($user, 'api') -> json('DELETE', route('seasons.delete', $new_season_add_content -> id));
        $new_season_delete_response -> assertStatus(200);
        $new_season_delete_content = json_decode($new_season_delete_response ->getContent());

        // CHECK DELETED_AT DATE SHOULD NOT EQUAL TO NULL
        $this -> assertNotNull($new_season_delete_content -> deleted_at);

        // CHECK IF THE NEW DELETED SEASON DOESN'T APPEAR IN ELOQUENT
        $exists = Season::find($new_season_add_content -> id);
        $this -> assertNull($exists);
    }

    public function test_restore_method () {
        $user = User::first();
        // INSERT THE NEW SEASON
        $new_season = Season::create([
            'name'          => 'season restore',
            'starting_date' => '2018-10-14',
            'ending_date'   => '2018-10-28',
            'created_by'    => $user -> id
        ]);

        // DELETE THE SEASON
        $new_season -> delete();

        // CHECK IF DELETED
        $exists = Season::find($new_season -> id);
        $this -> assertNull($exists);

        // RESTORING THE NEW DELETED SEASON
        $response = $this -> actingAs($user, 'api') -> json('GET', route('seasons.restore', $new_season -> id));
        $response -> assertStatus(200);

        // CHECK IF RESTORED
        $exists = Season::find($new_season -> id);
        $this -> assertNotNull($exists);
    }
}
