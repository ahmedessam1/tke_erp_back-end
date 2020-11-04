<?php

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'مدير المبيعات',
            'مندوب',
            'محاسب',
            'مشتريات',
            'صاحب الشركة',
            'مدير القسم',
            'نائب مدير القسم',
            'لوجستيك',
        ];
        for ($i = 0; $i < count($data); $i++) {
            Position::firstOrCreate([
                'name' => $data[$i]
            ]);
        }
    }
}
