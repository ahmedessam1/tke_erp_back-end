<?php

namespace App\Http\Controllers\Reports;

use App\Http\Requests\Reports\ProductsReports\CategorySalesReportRequest;
use App\Http\Requests\Reports\ProductsReports\HistoryRequest;
use App\Repositories\Reports\Contracts\ReportProductRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

class ProductsReportController extends Controller
{
    protected $model;

    public function __construct(ReportProductRepository $reports)
    {
        $this->model = $reports;
    }

    public function exportProductsCredit(Request $request)
    {
        // RETURNING THE EXCEL DATA
        return $this->model->exportProductsCredit($request);
    }

    public function productHistory(HistoryRequest $request)
    {
        return $this->model->productHistory($request);
    }

    public function topSoldByAmount(Request $request)
    {
        return $this->model->topSoldByAmount($request->year);
    }

    public function topSoldRepeatedly(Request $request)
    {
        return $this->model->topSoldRepeatedly($request->year);
    }

    public function topSoldProfit(Request $request)
    {
        return $this->model->topSoldProfit($request->type);
    }

    public function exportSupplierProductsCredit(Request $request)
    {
        // RETURNING THE EXCEL DATA
        return $this->model->exportSupplierProductsCredit($request);
    }

    public function sales(CategorySalesReportRequest $request)
    {
        // RETURNING THE EXCEL DATA
        return $this->model->sales($request);
    }
}
