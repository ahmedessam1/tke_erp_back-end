<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\ExpensesStoreRequest;
use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\ExpensesRepository;
use Illuminate\Http\Request;
use Response;

class ExpensesController extends Controller
{
    protected $model;

    public function __construct(ExpensesRepository $model)
    {
        $this->model = $model;
    }

    public function index (Request $request) {
        return Response::json($this->model->index($request->all()));
    }

    public function search (TableSearchRequest $request) {
        return Response::json($this->model->search($request->all()));
    }

    public function store(ExpensesStoreRequest $request)
    {
        return Response::json($this->model->store($request->all()));
    }

    public function delete ($item_id) {
        return Response::json($this->model->delete($item_id));
    }
}
