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
        $flag = User::where('email', 'admin@tradekeyegypt.com')->exists();
        if (!$flag) {
            User::create([
                'name' => 'ADMIN',
                'email' => 'admin@tradekeyegypt.com',
                'password' => bcrypt('secret'),
            ]);
        }
    }
}
