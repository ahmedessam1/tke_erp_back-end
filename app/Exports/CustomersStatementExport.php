<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class CustomersStatementExport implements FromView, ShouldAutoSize
{
    private $data;
    public function __construct($data) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.customers_statement', ['data' => $this->data]);
    }
}
