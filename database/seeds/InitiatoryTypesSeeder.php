<?php

use Illuminate\Database\Seeder;

class InitiatoryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'اضافة رصيد لمنتج',
            'اضافة رصيد لمورد',
            'اضافة رصيد لفرع عميل',
        ];
        for ($i = 0; $i < count($data); $i++) {
            \App\Models\Requirements\InitiatoryType::firstOrCreate([
                'type' => $data[$i]
            ]);
        }
    }
}
