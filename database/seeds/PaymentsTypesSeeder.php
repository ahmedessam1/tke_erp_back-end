<?php

use Illuminate\Database\Seeder;
use App\Models\Requirements\PaymentType;

class PaymentsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'كاش',
            'شيك',
        ];
        for ($i = 0; $i < count($data); $i++) {
            PaymentType::firstOrCreate([
                'type' => $data[$i]
            ]);
        }
    }
}
