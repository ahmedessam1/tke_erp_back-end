<?php

namespace App\Repositories\Reports;

use App\Cache\RedisAdapter;
use App\Exports\CustomersStatementExport;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerBranch;
use App\Models\Customer\CustomerContract;
use App\Models\Customer\CustomerInitiatoryCredit;
use App\Models\Customer\CustomerPayment;
use App\Models\Expenses\Expenses;
use App\Models\Invoices\ExportInvoice;
use App\Models\Product\SoldProducts;
use App\Models\Refund\Refund;
use App\Repositories\Reports\Contracts\ReportSalesRepository;
use App\Traits\Repository\Reports\Sales\SalesPerPeriod;
use App\Traits\Repository\Reports\Sales\SellersProgress;
use App\User;
use Excel;
use Auth;
use DB;

class EloquentReportSalesRepository implements ReportSalesRepository
{
    use SellersProgress, SalesPerPeriod;
    protected $cache;

    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function customerBranchSalesCompare($request)
    {
        $customer_branch_ids = CustomerBranch::whereIn('customer_id', $request->customers_id)->pluck('id');
        $year = $request->year;
        return $this->getMonthsSalesForCustomerBranch($customer_branch_ids, $year, ['sales', 'refunds']);
    }

    public function customerBranchProductsWithdrawals($request)
    {
        $customer_id = $request->customer_id;
        $customer_branch_id = $request->customer_branch_id;

        // GET INVOICES
        if ($customer_id !== null) {
            $branches_ids = CustomerBranch::where('customer_id', $customer_id)->pluck('id')->toArray();
            $export_invoices = ExportInvoice::withSoldProductsImages()
                ->approved()
                ->withSeller()
                ->whereIn('customer_branch_id', $branches_ids)
                ->whereBetween('date', [$request->from_date, $request->to_date])
                ->get();
        } else if ($customer_branch_id !== null)
            $export_invoices = ExportInvoice::withSoldProductsImages()
                ->approved()
                ->withSeller()
                ->where('customer_branch_id', $request->customer_branch_id)
                ->whereBetween('date', [$request->from_date, $request->to_date])
                ->get();

        // GET PRODUCTS DATA
        $export_invoice_ids = [];
        foreach ($export_invoices as $e_i)
            array_push($export_invoice_ids, $e_i->id);

        if ($request->categories_id)
            $products = SoldProducts::withProductAndImages()->whereIn('export_invoice_id', $export_invoice_ids)
                ->whereHas('product', function ($query) use ($request) {
                    $query->whereIn('category_id', $request->categories_id);
                })
                ->get();
        else
            $products = SoldProducts::withProductAndImages()->whereIn('export_invoice_id', $export_invoice_ids)->get();

        // ARRANGE DATA
        $temp_array = [];
        foreach ($products as $key => $row)
            $temp_array[$key] = $row['product_id'];
        $products = $products->toArray();
        array_multisort($temp_array, SORT_ASC, $products);

        // REPORT DATA
        $customer_name = '';
        if ($customer_id !== null) {
            $customer = Customer::find($customer_id);
            $customer_name = $customer->name;
        } else if ($customer_branch_id !== null) {
            $customer = CustomerBranch::find($request->customer_branch_id);
            $customer_name = $customer->customer_and_branch;
        }
        $report_data = [
            'customer' => $customer_name,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];

        return [
            'invoices' => $export_invoices,
            'products' => $products,
            'report_data' => $report_data,
        ];
    }

    public function yearlySales($years, $filters)
    {
        $result = $this->cache->remember('yearly_sales_report:' . implode(':', $years) . implode(':', $filters),
            function () use ($years, $filters) {
                $report_data = [];

                for ($y = 0; $y < count($years); $y++) {
                    $yearly_data = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $sales = 0;
                        $refunds_from_customers = 0;
                        $filters_counter = count($filters);

                        if (in_array('sales', $filters) || $filters_counter == 0)
                            $sales = $this->calculateYearlySales($years, $y, $i);

                        if (in_array('refunds_from_customers', $filters) || $filters_counter == 0)
                            $refunds_from_customers = $this->calculateYearlyRefunds($years, $y, $i, 'in');

                        $result = $sales - $refunds_from_customers;
                        array_push($yearly_data, round($result));
                    }
                    $report_data[$years[$y]] = $yearly_data;
                }
                return json_encode($report_data);
            }, config('constants.cache_expiry_minutes'));
        return json_decode($result, 1);
    }

    public function customerSalesAndRefunds($customer_id, $year)
    {
        $result = $this->cache->remember('customer_sales_and_refunds:' . $year . $customer_id,
            function () use ($year, $customer_id) {
                // GET CUSTOMER BRANCHES SALES
                $customer_branches = CustomerBranch::where('customer_id', $customer_id)->get();
                $branches_ids = [];
                foreach ($customer_branches as $customer_branch)
                    array_push($branches_ids, $customer_branch->id);

                $sales = $this->getMonthsSalesForCustomerBranch($branches_ids, $year, ['sales']);
                $refunds = $this->getMonthsSalesForCustomerBranch($branches_ids, $year, ['refunds']);
                $profit = $this->getMonthsSalesForCustomerBranch($branches_ids, $year, ['sales', 'refunds']);

                return json_encode([
                    'profit' => $profit,
                    'sales' => $sales,
                    'refunds' => $refunds,
                ]);
            }, config('constants.cache_expiry_minutes'));
        return json_decode($result, 1);
    }

    public function customersStatement($customers_id, $from_date, $to_date)
    {
        $this->cache->forget('customers_statement:' . implode(':', $customers_id) . $from_date . $to_date);
        $result = $this->cache->remember('customers_statement:' . implode(':', $customers_id) . $from_date . $to_date,
            function () use ($customers_id, $from_date, $to_date) {

                $data = [];
                for ($x = 0; $x < count($customers_id); $x++) {
                    // GET CUSTOMER BRANCHES
                    $customer = Customer::getBranchesDetails()->find($customers_id[$x]);
                    $customer_branches = $customer->branches;
                    $holder = [];

                    // GET CUSTOMER BRANCHES INVOICES
                    for ($y = 0; $y < count($customer_branches); $y++) {
                        $branch_invoices = ExportInvoice::approved()
                            ->withCustomerBranch()
                            ->where('customer_branch_id', $customer_branches[$y]->id)
                            ->whereBetween('date', [$from_date, $to_date])
                            ->get();

                        foreach ($branch_invoices as $invoice)
                            array_push($holder, [
                                'type' => 'export_invoice',
                                'branch_name' => $invoice->customerBranch->customer_and_branch,
                                'invoice_number' => $invoice->number,
                                'total' => $invoice->total_after_tax,
                                'date' => $invoice->date
                            ]);
                    }

                    // GET CUSTOMER BRANCHES REFUNDS
                    for ($z = 0; $z < count($customer_branches); $z++) {
                        $branch_refunds_invoices = Refund::approved()
                            ->withCustomerBranch()
                            ->where('model_id', $customer_branches[$z]->id)
                            ->where('type', 'in')
                            ->whereBetween('date', [$from_date, $to_date])
                            ->get();

                        foreach ($branch_refunds_invoices as $invoice)
                            array_push($holder, [
                                'type' => 'refund_invoice',
                                'branch_name' => $invoice->customerBranch->customer_and_branch,
                                'invoice_number' => $invoice->number,
                                'total' => $invoice->total_after_tax,
                                'date' => $invoice->date
                            ]);
                    }

                    // GET CUSTOMER PAYMENTS
                    $payments = CustomerPayment::Approved()
                        ->where('customer_id', $customers_id[$x])
                        ->whereBetween('date', [$from_date, $to_date])
                        ->get();

                    foreach ($payments as $payment) {
                        if ($payment->check_number !== null)
                            $description = $payment->check_number;
                        else
                            $description = $payment->notes;

                        // SUBTRACTING CONTRACT PERCENTAGE FROM PAYMENTS
                        $contract = CustomerContract::where('customer_id', $customers_id[$x])
                            ->where('year', date('Y', strtotime($payment->date)))
                            ->orderBy('id',  'DESC')
                            ->first();

                        $payment_amount = $payment->amount;
                        if($contract) {
                            $payment_amount = $payment_amount * ((100 - $contract->discount) / 100);
                            $description .= '('.trans('reports.CUSTOMERS.CONTRACT_DISCOUNT').'%'.$contract->discount.')';
                        }

                        array_push($holder, [
                            'type' => 'payment',
                            'branch_name' => $customer->name,
                            'invoice_number' => $description,
                            'total' => $payment_amount,
                            'date' => $payment->date
                        ]);
                    }

                    // INITIATORY CREDIT
                    $initiatory_credit = CustomerInitiatoryCredit::where('customer_id', $customers_id[$x])->get();
                    foreach ($initiatory_credit as $i_c) {
                        array_push($holder, [
                            'type' => 'initiatory_credit',
                            'branch_name' => $customer->name,
                            'invoice_number' => '-',
                            'total' => $i_c->amount,
                            'date' => $i_c->date
                        ]);
                    }


                    // GET CUSTOMER EXPENSES
                    $expenses = Expenses::approved()
                        ->withCustomer()
                        ->where('customer_id', $customers_id[$x])
                        ->whereBetween('date', [$from_date, $to_date])
                        ->get();

                    foreach ($expenses as $expense)
                        array_push($holder, [
                            'type' => 'expenses',
                            'branch_name' => $customer->name,
                            'invoice_number' => '-',
                            'total' => $expense->amount,
                            'date' => $expense->date
                        ]);

                    $data[$customer->name] = $holder;
                }
                // SORTING BY DATE
                foreach ($data as $key => $d)
                    $this->sortArrayByValue($data[$key], 'date');

                return json_encode($data);
            }, config('constants.cache_expiry_minutes'));

        return Excel::download(new CustomersStatementExport(json_decode($result, 1)),
            trans('excel.customers_statement.excel_file_name') . ' (' . $from_date . '~~' . $to_date . ').xlsx');
    }

    public function sellersProgress($year, $seller_id, $types)
    {
        if (Auth::user()->hasRole(['sales']))
            $seller_id = $this->getAuthUserId();
        $result = $this->cache->remember('sellers_progress_report:' . implode(':', $types) . $seller_id . $year,
            function () use ($year, $seller_id, $types) {
                $data = [];
                // GET SELLER NAME
                $seller_name = User::find($seller_id)->name;

                // GET SALES, REFUNDS AND PROFIT
                $sales_invoices = [];
                $refunds_invoices = [];

                if (in_array('sales', $types))
                    $sales_invoices = $this->sellerInvoices($seller_id, $year, 'sales');
                else if (in_array('refunds', $types))
                    $refunds_invoices = $this->sellerInvoices($seller_id, $year, 'refunds');

                array_push($data, [
                    'seller' => $seller_name,
                    'data' => $this->getSellerProgress($seller_id, $year, $types),
                    'sales_invoices' => $sales_invoices,
                    'refunds_invoices' => $refunds_invoices,
                    'customers_progress_percentage' => $this->getSellerProgressPerCustomerPercentage($sales_invoices),
                    'branches_sales' => $this->getSellerProgressBranchesSalesAndRefunds($seller_id, $year, ['sales']),
                    'branches_refunds' => $this->getSellerProgressBranchesSalesAndRefunds($seller_id, $year, ['refunds']),
                    'branches_profit' => $this->getSellerProgressBranchesSalesAndRefunds($seller_id, $year, ['sales', 'refunds']),
                ]);

                return json_encode($data);
            }, config('constants.cache_expiry_minutes'));
        return json_decode($result, 1);
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */



    /**
     * @param $array
     * @param $column
     */
    private function sortArrayByValue(&$array, $column)
    {
        $reference_array = [];

        foreach ($array as $key => $row)
            $reference_array[$key] = $row[$column];

        array_multisort($reference_array, SORT_ASC, $array);
    }
}
