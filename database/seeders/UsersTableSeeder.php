<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::table('role_user')->truncate();
        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Role::insert([
            [
                'role_name' => 'administrator',
                'display_name' => 'Administrator',
            ],
            [
                'role_name' => 'cashier',
                'display_name' => 'Cashier',
            ],
        ]);
        DB::commit();
        $user = User::create([
            'name' => 'Admin Testing',
            'email' => 'admin@testing.com',
            'password' => bcrypt('password'),
        ]);

        $adminRole = Role::where('role_name', 'administrator')->first();
        $user->roles()->attach($adminRole);
    }
}
