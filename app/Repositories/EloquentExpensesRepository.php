<?php

namespace App\Repositories;

use App\Models\Expenses\Expenses;
use App\Repositories\Contracts\ExpensesRepository;
use Auth;

class EloquentExpensesRepository implements ExpensesRepository
{
    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function index($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        return Expenses::orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function search($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return Expenses::where('title', 'LIKE', '%' . $q . '%')
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    function store($request)
    {
        $expenses_fillable_values = array_merge(
            $request,
            ['created_by' => $this->getAuthUserId()]
        );
        return Expenses::create($expenses_fillable_values);
    }

    public function delete($item_id)
    {
        $deleted = Expenses::notApproved()->find($item_id);
        if ($deleted)
            $deleted->delete();
        return $deleted;
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */
    private function setSorting($sort_by, $sort_type)
    {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}