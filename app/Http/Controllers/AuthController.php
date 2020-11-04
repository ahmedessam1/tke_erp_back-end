<?php

namespace App\Http\Controllers;

use App\Cache\RedisAdapter;
use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    public function login (Request $request) {
        $http = new \GuzzleHttp\Client;

        // CHECK IF EMAIL IS ACTIVE
        $active = User::where('email', $request -> email) -> where('active', 0) -> exists();
        if($active)
            return response()->json('This user is not active', 403);

        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request -> email,
                    'password' => $request -> password,
                ],
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400)
                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            else if ($e->getCode() === 401)
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    public function getUserDetails () {
        // GET USER DETAILS WITH ROLES AND PERMISSIONS.
        $user = Auth::user();
        $user -> getAllPermissions();

        return $user;
    }

    public function register (Request $request) {
        $this -> validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'permissions' => 'required',
        ]);
        return DB::transaction(function () use ($request) {
            // CREATE THE USER
            $user = User::create([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => Hash::make($request -> password),
            ]);

            // ASSIGN PERMISSIONS
            $user -> givePermissionTo($request -> permissions);

            return $user;
        });
    }

    public function permissions () {
        $permissions = Permission::pluck('name', 'id');
        return $permissions;
    }
}
