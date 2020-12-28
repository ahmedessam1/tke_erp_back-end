<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\GeneralRepository;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    protected $model;

    public function __construct(GeneralRepository $model)
    {
        $this->model = $model;
    }

    public function attachFileToModel(Request $request)
    {
        $this->validate($request, [
            'model_type' => 'required|in:import_invoice,export_invoice,refund_invoice'
        ]);
        return Response::json($this->model->attachFileToModel($request));
    }
}
