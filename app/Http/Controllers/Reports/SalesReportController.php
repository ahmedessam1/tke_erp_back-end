<?php

namespace App\Http\Controllers\Reports;

use App\Http\Requests\Reports\SalesReports\CustomerBranchProductsWithdrawalsReportRequest;
use App\Http\Requests\Reports\SalesReports\CustomerBranchReportRequest;
use App\Repositories\Reports\Contracts\ReportSalesRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

class SalesReportController extends Controller
{
    protected $model;

    public function __construct(ReportSalesRepository $reports)
    {
        $this->model = $reports;
    }

    public function customerBranchSalesCompare(CustomerBranchReportRequest $request)
    {
        return $this->model->customerBranchSalesCompare($request);
    }

    public function customerBranchProductsWithdrawals(CustomerBranchProductsWithdrawalsReportRequest $request)
    {
        return $this->model->customerBranchProductsWithdrawals($request);
    }

    public function yearlySales(Request $request)
    {
        return $this->model->yearlySales($request->years, $request->filters);
    }

    public function customerSalesAndRefunds(Request $request)
    {
        return $this->model->customerSalesAndRefunds($request->customer_id, $request->year);
    }

    public function customersStatement(Request $request)
    {
        return $this->model->customersStatement($request->customers_id, $request->from_date, $request->to_date);
    }

    public function sellersProgress(Request $request)
    {
        return $this->model->sellersProgress($request->year, $request->seller_id);
    }
}
