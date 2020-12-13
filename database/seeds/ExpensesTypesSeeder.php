<?php

use App\Models\Expenses\ExpensesTypes;
use Illuminate\Database\Seeder;

class ExpensesTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'بوفية مكتب',
            'بوفية مخزن',
            'استند/بوديوم/دعاية',
            'عمولات مناديب',
            'عمولات بائعين',
            'نقل بضائع',
            'بنزين سيارة',
            'قطع غيار سيارة',
            'ايجارات مخازن ومكاتب',
            'دفعات ضرائب',
        ];
        for ($i = 0; $i < count($data); $i++) {
            ExpensesTypes::firstOrCreate([
                'type' => $data[$i]
            ]);
        }
    }
}
