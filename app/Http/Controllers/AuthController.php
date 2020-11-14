<?php

namespace App\Http\Controllers;

use App\Cache\RedisAdapter;
use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
            'password' => 'required|string|min:4',
            'role_id' => 'required|exists:roles,id',
        ]);
        return DB::transaction(function () use ($request) {
            // CREATE THE USER
            $user = User::create([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => Hash::make($request -> password),
            ]);
            // ASSIGN ROLE TO USER
            $user->assignRole($request->role_id);
            return $user;
        });
    }

    public function edit ($user_id) {
        $user = User::with('roles')->find($user_id);
        return $user;
    }

    public function update (Request $request, $user_id) {
        $this -> validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
            'role_id' => 'required|exists:roles,id',
        ]);
        return DB::transaction(function () use ($request, $user_id) {
            // UPDATE USER
            $user = User::find($user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            // ASSIGN ROLE TO USER
            DB::table('model_has_roles')->where('model_id', $user_id)->delete();
            $user->assignRole($request->role_id);
            return $user;
        });
    }

    public function updatePassword (Request $request, $user_id) {
        $this -> validate($request, ['password' => 'required|string|min:4']);
        // UPDATE USER PASSWORD
        $user = User::find($user_id);
        $user -> password = Hash::make($request->password);
        $user->save();
        return $user;
    }

    public function roles () {
        return Role::pluck('name', 'id');
    }
}
