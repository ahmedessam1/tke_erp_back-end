<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CREATE ADMIN ROLE
        $roles = [
            'super_admin',
            'admin',
            'sales',
            'accountant_manager',
            'accountant',
            'data_entry',
            'tax',
            'warehouse',
            'graphic_designer',
            'digital_marketing',
        ];

        for ($x = 0; $x < count($roles); $x++) {
            $flag = Role::where('name', $roles[$x])->exists();
            if(!$flag) {
                $role = new Role;
                $role -> name = $roles[$x];
                $role -> guard_name = 'web';
                $role -> save();
            }
        }
    }
}
