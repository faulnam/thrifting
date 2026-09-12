<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN
        // Real Admin Account (Permanent)
        User::updateOrCreate(
            ['email' => 'admin@fifa.com'],
            [
                'name' => 'Admin Store (Real)',
                'phone' => '081234567890',
                'password' => Hash::make('qwertyu123'),
                'role' => 'admin',
                'is_demo' => false,
                'email_verified_at' => now(),
            ]
        );

        // Demo Admin Account (10-Min Auto Cleanup)
        User::updateOrCreate(
            ['email' => 'demo.admin@fifa.test'],
            [
                'name' => 'Demo Admin',
                'phone' => '081200000001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_demo' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. CUSTOMER
        // Real Customer Account (Permanent)
        User::updateOrCreate(
            ['email' => 'customer@fifa.com'],
            [
                'name' => 'Customer (Real)',
                'phone' => '081987654321',
                'password' => Hash::make('qwertyu123'),
                'role' => 'customer',
                'is_demo' => false,
                'email_verified_at' => now(),
            ]
        );

        // Demo Customer Account (10-Min Auto Cleanup)
        User::updateOrCreate(
            ['email' => 'demo.customer@fifa.test'],
            [
                'name' => 'Demo Customer',
                'phone' => '081900000001',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_demo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
