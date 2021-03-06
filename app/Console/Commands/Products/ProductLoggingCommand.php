<?php

namespace App\Console\Commands\Products;

use App\Models\Product\Product;
use App\Models\Product\ProductLog;
use App\Tenant\Models\Tenant;
use App\Traits\Eloquent\Products\ProductCalculationsTrait;
use Illuminate\Console\Command;
use Exception;
use DB;

class ProductLoggingCommand extends Command
{
    use ProductCalculationsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:log {tenant_domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logging product calculations (Average purchase price, Average sell price, Quantity, etc...)';

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

        $this->info('Logging products. This might take a while...');
        try {
            config()->set('database.default', 'tenant');
            foreach($tenants as $tenant) {
                config()->set('database.connections.tenant.database', 'tke_'.$tenant->name);
                DB::purge('tenant');
                $products = Product::withTrashed()->get();
                foreach ($products as $product) {
                    ProductLog::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'available_quantity' => $this->calculateProductAvailableQuantity($product->id),
                            'average_purchase_price' => $this->calculateProductAvgPurchasePrice($product->id),
                            'average_sell_price' => $this->calculateProductAvgSellPrice($product->id),
                        ]
                    );
                }
                $this->info("Done logging products for tenant: $tenant->domain");
            }
        } catch (Exception $e) {
            $this->error('Something went wrong! '.$e);
        }
    }
}
