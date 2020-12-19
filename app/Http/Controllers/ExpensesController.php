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

    public function index(Request $request)
    {
        return Response::json($this->model->index($request->all()));
    }

    public function search(TableSearchRequest $request)
    {
        return Response::json($this->model->search($request->all()));
    }

    public function show($item_id)
    {
        return Response::json($this->model->show($item_id));
    }

    public function store(ExpensesStoreRequest $request)
    {
        return Response::json($this->model->store($request->all()));
    }

    public function update($id, ExpensesStoreRequest $request)
    {
        return Response::json($this->model->update($id, $request->all()));
    }

    public function approve($item_id)
    {
        return Response::json($this->model->approve($item_id));
    }

    public function delete($item_id)
    {
        return Response::json($this->model->delete($item_id));
    }
}
