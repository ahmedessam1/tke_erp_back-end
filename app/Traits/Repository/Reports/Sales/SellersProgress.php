<?php

namespace App\Traits\Repository\Reports\Sales;

use App\Models\Customer\CustomerBranch;
use App\Models\Invoices\ExportInvoice;
use App\Models\Refund\Refund;

trait SellersProgress {
    /**
     * @param $seller_id
     * @param $year
     * @param $types
     * @return array
     */
    public function getSellerProgress($seller_id, $year, $types)
    {
        $sum = [];
        for ($i = 0; $i < 12; $i++) {
            $monthly_sum = 0;
            if (in_array('sales', $types)) {
                $temp_sales_invoice = ExportInvoice::approved()
                    ->where('seller_id', $seller_id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->get();
                foreach($temp_sales_invoice as $t_s_i)
                    $monthly_sum += $t_s_i->total_after_tax;
            }

            if (in_array('refunds', $types)) {
                $temp_refund_invoice = Refund::where('assigned_user_id', $seller_id)
                    ->approved()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->get();
                foreach($temp_refund_invoice as $t_r_i)
                    $monthly_sum -= $t_r_i->total_after_tax;
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
    public function sellerInvoices($seller_id, $year, $type)
    {
        $invoices = [];
        for ($i = 0; $i < 12; $i++) {
            if ($type === 'sales') {
                $invoice = ExportInvoice::withCustomerBranch()
                    ->approved()
                    ->withSeller()
                    ->where('seller_id', $seller_id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $i + 1)
                    ->get();
                array_push($invoices, $invoice);
            } else if ($type === 'refunds') {
                $invoice = Refund::approved()
                    ->where('assigned_user_id', $seller_id)
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
    public function getSellerProgressPerCustomerPercentage($sales_invoices) {
        $holder = [];
        $total_sales = 0;
        foreach($sales_invoices as $sales_invoice) {
            foreach($sales_invoice as $s_i) {
                $total_sales += $s_i->total_after_tax;
                $customer = $s_i->customerBranch;
                array_push($holder, [
                    'customer_id' => $customer->customer_id,
                    'customer_name' => $customer->customer->name,
                    'sales_total' => $s_i->total_after_tax,
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

    public function getSellerProgressBranchesSalesAndRefunds($seller_id, $year, $types)
    {
        $month_and_sum = [];
        $customer_branch_ids = CustomerBranch::whereHas('sellers', function ($query) use ($seller_id) {
            $query->where('seller_id', $seller_id);
        })->pluck('id');
        $counter = count($customer_branch_ids);

        for ($i = 0; $i < $counter; $i++) {
            $holder = [];

            for ($x = 0; $x < 12; $x++) {
                $sum = 0;
                $customer = CustomerBranch::find($customer_branch_ids[$i])->customer_and_branch;

                if (in_array('sales', $types)) {
                    $data = ExportInvoice::withCustomerBranch()
                        ->approved()
                        ->where('customer_branch_id', $customer_branch_ids[$i])
                        ->where('seller_id', $seller_id)
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
                        ->where('assigned_user_id', $seller_id)
                        ->where('type', 'in')
                        ->whereYear('date', $year)
                        ->whereMonth('date', $x + 1)
                        ->get();

                    foreach ($data as $d)
                        $sum -= $d->total_after_tax;
                }

                array_push($holder, [
                    'sum' => round($sum, 2),
                    'customer' => $customer,
                ]);
            }
            array_push($month_and_sum, $holder);
        }

        return $month_and_sum;
    }
}
