<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\User;
use Response;



class UsersTest extends TestCase
{
    use DatabaseTransactions;


    protected function response_structure_check ($response) {
        $response -> assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id', 'name', 'email', 'active', 'created_at',
                    'roles' => [
                        '*' => [
                            'id', 'name'
                        ]
                    ]
                ]
            ],
            'from', 'last_page', 'per_page', 'to', 'total'
        ]);
    }

    public function test_get_all_active_users_method () {
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> get(route('users.index'));
        $response -> assertStatus(200);

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);
    }

    public function test_searching_all_active_users_method () {
        // CREATE A NEW USER
        $new_user = User::create([
            'name' => 'Just For Test',
            'email' => 'just_for_test@system.com',
            'password' => 'just for test',
            'active' => 1
        ]);

        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> json('GET', route('users.search'), ["query"=>$new_user->name]);
        $response -> assertStatus(200);
        $content = json_decode($response->getContent());

        // RETURNED JSON STRUCTURE FOR ALL RESPONSE TEST
        $this -> response_structure_check($response);

        // CHECK FOR RESPONSE
        $this -> assertEquals($content -> data[0] -> email, $new_user -> email);
    }

    public function test_deactivating_single_user () {
        // CREATE A NEW USER
        $new_user = User::create([
            'name' => 'Just For Test',
            'email' => 'just_for_test@system.com',
            'password' => 'just for test',
            'active' => 1
        ]);
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> json('DELETE', route('users.deactivate', $new_user -> id));
        $response -> assertStatus(200);

        $content = json_decode($response->getContent());

        $this -> assertEquals($content->active, 0);
    }

    public function test_reactivating_single_user () {
        // CREATE A NEW USER
        $new_user = User::create([
            'name' => 'Just For Test',
            'email' => 'just_for_test@system.com',
            'password' => 'just for test',
            'active' => 0
        ]);
        // CHECK IF RETURN 200
        $user = User::first();
        $response = $this -> actingAs($user, 'api') -> json('GET', route('users.reactivate', $new_user -> id));
        $response -> assertStatus(200);

        $content = json_decode($response->getContent());

        $this -> assertEquals($content->active, 1);
    }
}
