<?php

namespace App\Repositories\Reports;

use App\Cache\RedisAdapter;
use App\Exports\CustomersStatementExport;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerBranch;
use App\Models\Customer\CustomerBranchesSellers;
use App\Models\Customer\CustomerInitiatoryCredit;
use App\Models\Customer\CustomerPayment;
use App\Models\Invoices\ExportInvoice;
use App\Models\Product\SoldProducts;
use App\Models\Refund\Refund;
use App\Repositories\Reports\Contracts\ReportSalesRepository;
use App\User;
use Excel;
use Auth;
use DB;

class EloquentReportSalesRepository implements ReportSalesRepository
{
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
                ->withSeller()
                ->whereIn('customer_branch_id', $branches_ids)
                ->whereBetween('date', [$request->from_date, $request->to_date])
                ->get();
        } else if ($customer_branch_id !== null)
            $export_invoices = ExportInvoice::withSoldProductsImages()
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
        $customer = CustomerBranch::find($request->customer_branch_id);
        $report_data = [
            'customer' => $customer->customer_and_branch,
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
                        array_push($holder, [
                            'type' => 'payment',
                            'branch_name' => $customer->name,
                            'invoice_number' => $description,
                            'total' => $payment->amount,
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

    public function sellersProgress($year, $sellers_id, $types)
    {
        if (Auth::user()->hasRole(['sales']))
            $sellers_id = [$this->getAuthUserId()];
        $result = $this->cache->remember('sellers_progress_report:' . implode(':', $types) . implode(':', $sellers_id) . $year,
            function () use ($year, $sellers_id, $types) {
                $data = [];

                for ($i = 0; $i < count($sellers_id); $i++) {
                    // GET SELLER NAME
                    $seller_name = User::find($sellers_id[$i])->name;

                    // GET SALES, REFUNDS AND PROFIT
                    $sales_invoices = [];
                    $refunds_invoices = [];

                    if (in_array('sales', $types))
                        $sales_invoices = $this->sellerInvoices($sellers_id[$i], $year, 'sales');
                    else if (in_array('refunds', $types))
                        $refunds_invoices = $this->sellerInvoices($sellers_id[$i], $year, 'refunds');

                    array_push($data, [
                        'seller' => $seller_name,
                        'data' => $this->getSellerProgress($sellers_id[$i], $year, $types),
                        'sales_invoices' => $sales_invoices,
                        'refunds_invoices' => $refunds_invoices,
                        'customers_progress_percentage' => $this->getSellerProgressPerCustomerPercentage($sales_invoices),
                    ]);
                }

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
     * @param $customer_branch_ids
     * @param $year
     * @param $types
     * @return array
     */
    private function getMonthsSalesForCustomerBranch($customer_branch_ids, $year, $types)
    {
        $month_and_sum = [];
        $counter = count($customer_branch_ids);

        for ($i = 0; $i < $counter; $i++) {
            $holder = [];

            for ($x = 0; $x < 12; $x++) {
                $sum = 0;
                $customer = CustomerBranch::find($customer_branch_ids[$i])->customer_and_branch;

                if (in_array('sales', $types)) {
                    $data = ExportInvoice::withCustomerBranch()
                        ->where('customer_branch_id', $customer_branch_ids[$i])
                        ->approved()
                        ->whereYear('date', $year)
                        ->whereMonth('date', $x + 1)
                        ->get();

                    $customer = CustomerBranch::find($customer_branch_ids[$i])->customer_and_branch;
                    foreach ($data as $d)
                        $sum += $d->total_after_tax;
                }

                if (in_array('refunds', $types)) {
                    $data = Refund::approved()
                        ->where('model_id', $customer_branch_ids[$i])
                        ->where('type', 'in')
                        ->whereYear('date', $year)
                        ->whereMonth('date', $x + 1)
                        ->get();

                    foreach ($data as $d)
                        $sum -= $d->total_after_tax;
                }

                array_push($holder, [
                    'sum' => round($sum),
                    'customer' => $customer,
                ]);
            }
            array_push($month_and_sum, $holder);
        }

        return $month_and_sum;
    }

    /**
     * @param $years
     * @param $y
     * @param $i
     * @return int
     */
    private function calculateYearlySales($years, $y, $i)
    {
        $sum = 0;
        $export_invoices = ExportInvoice::approved()
            ->whereYear('date', $years[$y])
            ->whereMonth('date', $i)
            ->orderBy('number')
            ->get();
        foreach ($export_invoices as $invoice)
            $sum += $invoice->total_after_tax;
        return $sum;
    }

    /**
     * @param $years
     * @param $y
     * @param $i
     * @param $type
     * @return int
     */
    private function calculateYearlyRefunds($years, $y, $i, $type)
    {
        $sum = 0;
        $refund_invoices = Refund::approved()
            ->where('type', $type)
            ->whereYear('date', $years[$y])
            ->whereMonth('date', $i)
            ->orderBy('number')
            ->get();
        foreach ($refund_invoices as $invoice)
            $sum += $invoice->total_after_tax;
        return $sum;
    }

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

    /**
     * @param $branches_id
     * @param $year
     * @param $types
     * @return array
     */
    private function getSellerProgress($seller_id, $year, $types)
    {
        $sum = [];
        for ($i = 0; $i < 12; $i++) {
            $monthly_sum = 0;
            if (in_array('sales', $types)) {
                $monthly_sum += ExportInvoice::where('seller_id', $seller_id)
                    ->approved()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->sum('net_total');
            }

            if (in_array('refunds', $types)) {
                $monthly_sum -= Refund::where('assigned_user_id', $seller_id)
                    ->approved()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->sum('net_total');
            }

            array_push($sum, $monthly_sum);
        }
        return $sum;
    }

    /**
     * @param $seller_id
     * @param $year
     * @param $type
     * @return array
     */
    private function sellerInvoices($seller_id, $year, $type)
    {
        $invoices = [];
        for ($i = 0; $i < 12; $i++) {
            if ($type === 'sales') {
                $invoice = ExportInvoice::withCustomerBranch()
                    ->withSeller()
                    ->where('seller_id', $seller_id)
                    ->approved()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->get();
                array_push($invoices, $invoice);
            } else if ($type === 'refunds') {
                $invoice = Refund::where('assigned_user_id', $seller_id)
                    ->approved()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->get();
                array_push($invoices, $invoice);
            }
        }
        return $invoices;
    }

    /**
     * @param $sales_invoices
     * @return array
     */
    private function getSellerProgressPerCustomerPercentage($sales_invoices) {
        $holder = [];
        $total_sales = 0;
        foreach($sales_invoices as $sales_invoice) {
            foreach($sales_invoice as $s_i) {
                $total_sales += $s_i->net_total;
                $customer = $s_i->customerBranch;
                array_push($holder, [
                    'customer_id' => $customer->customer_id,
                    'customer_name' => $customer->customer->name,
                    'sales_total' => $s_i->net_total,
                ]);
            }
        }

        $result = [];
        foreach($holder as $k => $v) {
            $id = $v['customer_id'];
            $result[$id]['customer_name'] = $v['customer_name'];
            $result[$id]['calculations'][] = $v['sales_total'];
        }
        $sorted_result = [];
        foreach($result as $key => $value) {
            $sorted_result[] = [
                'customer_id' => $key, 'total_sales' => $total_sales,
                'customer_name' => $value['customer_name'], 'sales_total' => array_sum($value['calculations'])
            ];
        }

        for($i = 0; $i < count($sorted_result); $i++) {
            $number = $sorted_result[$i]['sales_total'];
            $percentage = ($number*100)/$total_sales;
            $sorted_result[$i]['percentage'] = round($percentage, 2);
        }

        return $sorted_result;
    }
}
