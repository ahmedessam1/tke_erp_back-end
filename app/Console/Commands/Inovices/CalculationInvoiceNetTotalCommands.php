<?php

namespace App\Console\Commands\Inovices;

use App\Models\Invoices\ExportInvoice;
use App\Models\Invoices\ImportInvoice;
use App\Models\Refund\Refund;
use App\Traits\Logic\InvoiceCalculations;
use App\User;
use Illuminate\Console\Command;
use Auth;

class CalculationInvoiceNetTotalCommands extends Command
{
    use InvoiceCalculations;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:net_total {invoice_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiatory the invoices net total...';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $invoice_type = $this->argument('invoice_type');
        $this->info('This might take a while...');
        $admin_user = User::where('email', 'admin@tradekeyegypt.com')->first();
        Auth::loginUsingId($admin_user->id);
        if ($invoice_type === 'import_invoice') {
            $invoices = ImportInvoice::withProductCredits()->get();
            foreach ($invoices as $invoice) {
                $net_total = $invoice->invoiceTotal('import_invoice', $invoice->productCredits, 0, 0);
                $invoice->net_total = $net_total;
                $invoice->save();
            }
            $this->info('Import invoice net total has been updates successfully!');
        } else if ($invoice_type === 'export_invoice') {
            $invoices = ExportInvoice::with('soldProducts')->get();
            foreach ($invoices as $invoice) {
                $net_total = $invoice->invoiceTotal('export_invoice', $invoice->soldProducts, 0, 0);
                $invoice->net_total = $net_total;
                $invoice->save();
            }
            $this->info('Export invoice net total has been updates successfully!');
        } else if ($invoice_type === 'refund_invoice') {
            $invoices = Refund::with('refundedProducts')->get();
            foreach ($invoices as $invoice) {
                $net_total = $invoice->invoiceTotal('refund_invoice', $invoice->refundedProducts, 0, 0);
                $invoice->net_total = $net_total;
                $invoice->save();
            }
            $this->info('Refund invoice net total has been updates successfully!');
        } else
            $this->error('Something went wrong!');
    }
}
