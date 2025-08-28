<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'role' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@email.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'profile_image' => null,
                'remember_token' => \Illuminate\Support\Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => 'employee',
                'name' => 'Employee User',
                'email' => 'employee@email.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'profile_image' => null,
                'remember_token' => \Illuminate\Support\Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
