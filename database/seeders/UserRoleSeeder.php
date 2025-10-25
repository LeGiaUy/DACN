<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'role' => 'admin',
            'permissions' => [
                'manage_users',
                'manage_products',
                'manage_categories',
                'manage_brands',
                'view_dashboard',
                'manage_orders',
                'manage_settings'
            ],
            'is_active' => true,
            'phone' => '0123456789',
            'address' => '123 Admin Street, City'
            ]
        );

        // Create Moderator User
        User::updateOrCreate(
            ['email' => 'moderator@example.com'],
            [
            'name' => 'Moderator User',
            'email' => 'moderator@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'role' => 'moderator',
            'permissions' => [
                'manage_products',
                'manage_categories',
                'manage_brands',
                'view_dashboard',
                'manage_orders'
            ],
            'is_active' => true,
            'phone' => '0987654321',
            'address' => '456 Moderator Street, City'
            ]
        );

        // Create Regular User
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'role' => 'user',
            'permissions' => [
                'view_products',
                'view_categories',
                'view_brands',
                'place_orders'
            ],
            'is_active' => true,
            'phone' => '0555555555',
            'address' => '789 User Street, City'
            ]
        );

        // Create Inactive User
        User::updateOrCreate(
            ['email' => 'inactive@example.com'],
            [
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'role' => 'user',
            'permissions' => [],
            'is_active' => false,
            'phone' => '0111111111',
            'address' => '999 Inactive Street, City'
            ]
        );
    }
}
