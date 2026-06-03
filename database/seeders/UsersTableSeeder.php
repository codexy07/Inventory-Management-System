<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff User 1',
            'username' => null,
            'email' => 'staff1@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Staff User 2',
            'username' => null,
            'email' => 'staff2@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);
    }
}
