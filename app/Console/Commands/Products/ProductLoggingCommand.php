<?php

namespace App\Console\Commands\Products;

use App\Models\Product\Product;
use App\Models\Product\ProductLog;
use App\Traits\Eloquent\Products\ProductCalculationsTrait;
use Illuminate\Console\Command;
use Exception;

class ProductLoggingCommand extends Command
{
    use ProductCalculationsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:log';

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
        $this->info('Logging products. This might take a while...');
        try {
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
            $this->info('Done logging products.');
        } catch (Exception $e) {
            $this->error('Something went wrong! '.$e);
        }
    }
}
