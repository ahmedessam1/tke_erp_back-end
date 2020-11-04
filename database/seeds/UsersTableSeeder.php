<?php

use App\User;
use Illuminate\Database\Seeder;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flag = User::where('email', 'admin@tradekeyegypt.com') -> exists();
        if (!$flag) {
            $user = User::create([
                'name' => 'ADMIN',
                'email' => 'admin@tradekeyegypt.com',
                'password' => bcrypt('secret'),
            ]);
            $user -> assignRole('ادمن');
        } else {
            $user = User::where('email', 'admin@tradekeyegypt.com') -> first();

            // ASSIGN ALL PERMISSIONS
            $permissions = \Spatie\Permission\Models\Permission::all();
            $user_permissions = [];

            foreach($user -> getAllPermissions() as $user_permission) {
                array_push($user_permissions, $user_permission -> name);
            }

            foreach($permissions as $permission) {
                if (!in_array($permission -> name, $user_permissions)) {
                    $user -> givePermissionTo($permission -> name);
                }
            }
        }
    }
}
