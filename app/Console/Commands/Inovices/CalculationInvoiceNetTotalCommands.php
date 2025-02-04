<?php

namespace App\Console\Commands\Inovices;

use App\Models\Invoices\ExportInvoice;
use App\Models\Invoices\ImportInvoice;
use App\Models\Refund\Refund;
use App\Tenant\Models\Tenant;
use App\Traits\Logic\InvoiceCalculations;
use App\User;
use Illuminate\Console\Command;
use Auth;
use DB;

class CalculationInvoiceNetTotalCommands extends Command
{
    use InvoiceCalculations;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:net_total {invoice_type} {tenant_domain}';

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
        $tenant_argument_domain = $this->argument('tenant_domain');
        $tenants = [];
        if($tenant_argument_domain === 'all') {
            $tenants = Tenant::all();
        } else {
            $single_tenant = Tenant::where('domain', $tenant_argument_domain)->first();
            if($single_tenant)
                array_push($tenants, $single_tenant);
        }

        if(count($tenants) === 0) {
            $this->error('Wrong tenant domain.. Write the correct tenant domain or write "all" for all tenants!');
            return;
        }

        config()->set('database.default', 'tenant');
        foreach($tenants as $tenant) {
            config()->set('database.connections.tenant.database', 'tke_'.$tenant->name);
            DB::purge('tenant');

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

            $this->info("Done calculating invoices net total for tenant: $tenant->domain .......");
        }
    }
}
