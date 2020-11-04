<?php

namespace App\Http\Controllers;

use App\Imports\ProductCreditsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataController extends Controller
{
    public function productCredits (Request $request) {
        Excel::import(new ProductCreditsImport,request()->file('file'));

        return $request;
    }
}
