<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\RequirementRepository;
use Response;

class RequirementsController extends Controller
{
    protected $model;

    public function __construct(RequirementRepository $requirements)
    {
        $this->model = $requirements;
    }

    // GET USERS LIST
    public function users()
    {
        return Response::json($this->model->users());
    }

    // GET PAYMENT TYPES LIST
    public function paymentTypes()
    {
        return Response::json($this->model->paymentTypes());
    }

    // GET SUPPLIERS LIST
    public function suppliers()
    {
        return Response::json($this->model->suppliers());
    }

    // GET JOB POSITIONS LIST
    public function positions()
    {
        return Response::json($this->model->positions());
    }

    // GET CATEGORIES LIST
    public function categories()
    {
        return Response::json($this->model->categories());
    }

    // GET CATEGORIES LIST
    public function warehouses()
    {
        return Response::json($this->model->warehouses());
    }

    // GET CUSTOMERS LIST
    public function customers()
    {
        return Response::json($this->model->customers());
    }

    // GET CUSTOMERS BRANCHES LIST
    public function customersBranches()
    {
        return Response::json($this->model->customersBranches());
    }

    // GET PRODUCT DISMISS REASONS
    public function productDismissReasons()
    {
        return Response::json($this->model->productDismissReasons());
    }

    // GET PRODUCT DISMISS REASONS
    public function expensesTypes()
    {
        return Response::json($this->model->expensesTypes());
    }
}
