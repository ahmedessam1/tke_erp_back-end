<?php

namespace App\Imports;

use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductCreditWarehouses;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class ProductCreditsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $stop_flag = count($row);
            if ($stop_flag < 6)
                return;

            // $row[0] code ||| $row[4] quantity ||| $row[5] price
            $product = Product::where('code', $row[0]) -> first();
            $quantity = $row[4];
            $purchase_price = $row[5];
            $warehouse = Warehouse::first();
            $flag = ProductCredits::where('product_id', $product -> id) -> first();
            if ($product && $quantity && $purchase_price && $flag) {
                $product_credit = ProductCredits::create([
                    "import_invoice_id" => null,
                    "product_id"        => $product -> id,
                    "quantity"          => $quantity,
                    "package_size"      => null,
                    "purchase_price"    => $purchase_price,
                    "discount"          => 0,
                    "created_by"        => Auth::user() -> id,
                ]);

                ProductCreditWarehouses::create([
                    "product_credit_id"     => $product_credit -> id,
                    "warehouse_id"          => $warehouse -> id,
                    "created_by"            => Auth::user() -> id,
                ]);
            }
        });
    }
}
