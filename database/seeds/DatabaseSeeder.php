<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            RolePermissionSeeder::class,
            SuppliersSeeder::class,
            SeasonsSeeder::class,
            WarehousesSeeder::class,
            PaymentsTypesSeeder::class,
            PositionsSeeder::class,
            InitiatoryTypesSeeder::class,
            ProductDismissReasonsSeeder::class,
            ExpensesTypesSeeder::class,
        ]);
    }
}
