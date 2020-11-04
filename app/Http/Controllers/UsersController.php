<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\UserRepository;
use App\User;
use Response;
use Auth;

class UsersController extends Controller
{
    protected $users;
    public function __construct(UserRepository $users) {
        $this -> users = $users;
    }

    public function index(){
        // TESTED....
        $getUsers = $this -> users -> getAllActiveUsers();
        return Response::json($getUsers);
    }

    public function search (TableSearchRequest $request) {
        // TESTED
        $getUsers = $this -> users -> getUsersSearchResult($request -> input('query'));
        return Response::json($getUsers);
    }


    public function store()
    {
        // TODO: Store new user (UsersController@store)
    }


    public function show($id)
    {
        // TODO: Show user details (UsersController@show)
    }


    public function edit($id)
    {
        // TODO: Edit user details (UsersController@edit)
    }


    public function deactivate(User $user)
    {
        // TESTED....
        $deactivated_user = $this -> users -> deactivateUser($user);
        return Response::json($deactivated_user);
    }

    public function reactivate(User $user)
    {
        // TESTED....
        $reactivated_user = $this -> users -> reactivateUser($user);
        return Response::json($reactivated_user);
    }
}
