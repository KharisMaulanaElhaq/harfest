<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@harfest.test'],
            [
                'name' => 'Admin Harfest',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun Demo User / Pembeli
        User::updateOrCreate(
            ['email' => 'user@harfest.test'],
            [
                'name' => 'Pengunjung Harfest',
                'phone' => '089876543210',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'email_verified_at' => now(),
            ]
        );
    }
}
