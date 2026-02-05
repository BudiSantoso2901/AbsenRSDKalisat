<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Hb',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('rsdkalisat'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Sobri',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'santos666',
            'email' => 'user13@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123'),
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => 'Raes',
            'email' => 'user331@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123'),
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => 'Administrator',
            'email' => 'admin777@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123'),
            'remember_token' => Str::random(10),
        ]);
    }
}
