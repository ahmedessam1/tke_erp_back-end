<?php

namespace App\Traits\Repository\Reports\Sales;

use App\Models\Customer\CustomerBranch;
use App\Models\Invoices\ExportInvoice;
use App\Models\Refund\Refund;

trait SalesPerPeriod
{
    /**
     * @param $customer_branch_ids
     * @param $year
     * @param $types
     * @return array
     */
    public function getMonthsSalesForCustomerBranch($customer_branch_ids, $year, $types)
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
    public function calculateYearlySales($years, $y, $i)
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
    public function calculateYearlyRefunds($years, $y, $i, $type)
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
}
