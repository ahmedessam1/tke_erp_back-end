<?php

namespace App\Repositories\Contracts;

interface ExpensesRepository
{
    public function index($request);

    public function search($request);

    public function store($request);

    public function delete($item_id);
}