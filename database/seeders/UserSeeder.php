<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@claims.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        DB::table('users')->updateOrInsert(
            ['username' => 'user'],
            [
                'name' => 'User',
                'email' => 'user@claims.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
