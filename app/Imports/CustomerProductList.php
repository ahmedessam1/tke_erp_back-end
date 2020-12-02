<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Customer\CustomerPriceList;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Product\Product;
use Auth;
use DB;

class CustomerProductList implements ToModel, WithStartRow
{
    private $customer_id;

    public function  __construct($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function() use ($row) {
            if ($row[0]) {
                // CHECK IF PRODUCT EXIST WITH BARCODE
                $product = Product::where('barcode', $row[0])->first();

                if ($product) {
                    CustomerPriceList::updateOrCreate([
                        'customer_id' => $this->customer_id,
                        'product_id' => $product->id,
                    ],[
                        'customer_id' => $this->customer_id,
                        'product_id' => $product->id,
                        'product_selling_price' => $row[2],
                        'product_name' => $row[1],
                        'product_barcode' => $row[0],
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }
        });
    }

    public function startRow(): int
    {
        return 2;
    }
}
