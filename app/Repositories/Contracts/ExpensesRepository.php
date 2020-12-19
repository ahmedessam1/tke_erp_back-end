<?php

namespace App\Repositories\Contracts;

interface ExpensesRepository
{
    public function index($request);

    public function search($request);

    public function show($item_id);

    public function store($request);

    public function update($id, $request);

    public function approve($item_id);

    public function delete($item_id);
}
