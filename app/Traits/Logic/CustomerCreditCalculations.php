<?php

namespace App\Traits\Logic;

use App\Models\Customer\CustomerBranch;
use App\Models\Customer\CustomerPayment;
use App\Models\Customer\CustomerInitiatoryCredit;
use App\Models\Invoices\ExportInvoice;
use App\Models\Refund\Refund;

trait CustomerCreditCalculations {
    private $invoices_total;
    private $payments_total;
    private $initiatory_total;
    private $refunds_total;

    // GET THE CUSTOMER INVOICES TOTAL
    public function customerInvoicesTotal ($customer_id) {
        $branches_ids = [];
        $branches = CustomerBranch::select('id') -> where('customer_id', $customer_id) -> get();
        foreach($branches as $branch)
            array_push($branches_ids, $branch -> id);

        $invoices = ExportInvoice::approved() -> whereIn('customer_branch_id', $branches_ids) -> get();

        $this -> invoices_total = 0;
        foreach($invoices as $invoice) {
            $this -> invoices_total += $this -> invoiceTotal(
                'export_invoice',
                $invoice -> soldProducts,
                $invoice -> tax,
                $invoice -> discount
            );
        }
        return $this -> invoices_total;
    }

    // GET THE CUSTOMER PAYMENTS TOTAL
    public function customerPaymentsTotal ($customer_id) {
        $payments = CustomerPayment::approved() -> where('customer_id', $customer_id) -> get();

        $this -> payments_total = 0;
        foreach($payments as $payment) {
            $this -> payments_total += $payment -> amount;
        }

        return $this -> payments_total;
    }

    // GET THE CUSTOMER BRANCH INITIATORY PAYMENTS TOTAL
    public function customerInitiatoryCreditTotal ($customer_id) {
        $initiatory_total = CustomerInitiatoryCredit::where('customer_id', $customer_id)
            -> groupBy('customer_id')
            -> sum('amount');

        $this -> initiatory_total = $initiatory_total;
        return $this -> initiatory_total;
    }

    // GET CUSTOMER REFUNDS TOTAL
    public function customerRefundsTotal ($customer_id) {
        // GET CUSTOMER BRANCHES IDs
        $branches_ids = [];
        $branches = CustomerBranch::select('id') -> where('customer_id', $customer_id) -> get();
        foreach($branches as $branch)
            array_push($branches_ids, $branch -> id);

        $total = 0;
        $refunds = Refund::approved()
            -> withRefundedProducts()
            -> whereIn('model_id', $branches_ids)
            -> where('type', 'in')
            -> get();
        foreach($refunds as $refund)
            foreach($refund -> refundedProducts as $r_p)
                $total += ($r_p -> price * $r_p -> quantity);
        $this -> refunds_total = $total;
        return $this -> refunds_total;
    }

    // GET CUSTOMER BRANCH NET CREDIT
    public function netCredit () {
        return ($this -> invoices_total + $this -> initiatory_total) - ($this -> payments_total + $this -> refunds_total);
    }
}