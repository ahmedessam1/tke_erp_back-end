<?php

use Illuminate\Database\Seeder;
use App\Models\ProductDismissReasons;

class ProductDismissReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'توالف',
            'خطاء في الجرد',
            'مفقود',
        ];
        for ($i = 0; $i < count($data); $i++) {
            ProductDismissReasons::firstOrCreate([
                'reason' => $data[$i]
            ]);
        }
    }
}
