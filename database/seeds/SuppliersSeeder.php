<?php

use Illuminate\Database\Seeder;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierAddress;
use App\Models\Supplier\SupplierAddressContact;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $user_id = 1;
//        $supplier = Supplier::create([
//            'name'          => str_random(10),
//            'created_by'    => $user_id,
//        ]);
//
//        $supplier_address = SupplierAddress::create([
//            'supplier_id'   => $supplier -> id,
//            'address'       => str_random(30),
//            'created_by'    => $user_id,
//        ]);
//
//        for($x = 0; $x < 2; $x++)
//            SupplierAddressContact::create([
//                'supplier_address_id'   => $supplier_address -> id,
//                'name'                  => str_random(10),
//                'phone_number'          => str_random(11),
//                'created_by'            => $user_id,
//            ]);
    }
}
