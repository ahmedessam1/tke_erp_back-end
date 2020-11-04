<?php

namespace App\Exports;

use App\Models\Refund\Refund;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Invoices\ExportInvoice as ExportInvoiceModel;
use App\Models\Invoices\ImportInvoice as ImportInvoiceModel;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportInvoice implements FromView, ShouldAutoSize
{
    private $invoice_id, $type;

    public function __construct($type, $invoice_id)
    {
        $this->type = $type;
        $this->invoice_id = $invoice_id;
    }

    public function view(): View
    {
        $invoices = null;
        if ($this->type === 'exports')
            $invoices = ExportInvoiceModel::with('soldProducts')->where('id', $this->invoice_id)->get();
        else if ($this->type === 'imports')
            $invoices = ImportInvoiceModel::with('productCredits')->where('id', $this->invoice_id)->get();
        else if ($this->type === 'refunds')
            $invoices = Refund::with('refundedProducts')->where('id', $this->invoice_id)->get();
        return view('exports.invoices', [
            'type' => $this->type,
            'invoices' => $invoices,
        ]);
    }
}
