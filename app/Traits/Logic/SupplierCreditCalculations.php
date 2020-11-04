<?php

namespace App\Traits\Logic;

use App\Models\Invoices\ImportInvoice;
use App\Models\Refund\Refund;
use App\Models\Supplier\SupplierInitiatoryCredit;
use App\Models\Supplier\SupplierPayment;

trait SupplierCreditCalculations {
    private $invoices_total;
    private $payments_total;
    private $initiatory_total;
    private $refunds_total;

    // GET THE SUPPLIER INVOICES TOTAL
    public function supplierInvoicesTotal ($supplier_id) {
        $invoices = ImportInvoice::approved() -> where('supplier_id', $supplier_id) -> get();

        $this -> invoices_total = 0;
        foreach($invoices as $invoice) {
            $this -> invoices_total += $this -> invoiceTotal(
                'import_invoice',
                $invoice -> productCredits,
                $invoice -> tax,
                $invoice -> discount
            );
        }
        return $this -> invoices_total;
    }

    // GET THE SUPPLIER PAYMENTS TOTAL
    public function supplierPaymentsTotal ($supplier_id) {
        $payments = SupplierPayment::approved() -> where('supplier_id', $supplier_id) -> get();

        $this -> payments_total = 0;
        foreach($payments as $payment) {
            $this -> payments_total += $payment -> amount;
        }
        return $this -> payments_total;
    }

    // GET THE SUPPLIER PAYMENTS TOTAL
    public function supplierInitiatoryCreditTotal ($supplier_id) {
        $initiatory_total = SupplierInitiatoryCredit::where('supplier_id', $supplier_id)
            -> groupBy('supplier_id')
            -> sum('amount');

        $this -> initiatory_total = $initiatory_total;
        return $this -> initiatory_total;
    }

    // GET CUSTOMER REFUNDS TOTAL
    public function supplierRefundsTotal ($supplier_id) {
        $total = 0;
        $refunds = Refund::approved()
            -> withRefundedProducts()
            -> where('model_id', $supplier_id)
            -> where('type', 'out')
            -> get();
        foreach($refunds as $refund)
            foreach($refund -> refundedProducts as $r_p)
                $total += ($r_p -> price * $r_p -> quantity);
        $this -> refunds_total = $total;
        return $this -> refunds_total;
    }

    // GET SUPPLIER NET CREDIT
    public function netCredit () {
        return ($this -> invoices_total + $this -> initiatory_total) - ($this -> payments_total + $this -> refunds_total);
    }
}