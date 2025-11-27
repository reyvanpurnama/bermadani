<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing users (truncate gak bisa karena FK)
        User::query()->delete();

        // Create users for each role
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@test.com',
                'password' => Hash::make('password'),
                'role' => 'SUPER_ADMIN',
                'isActive' => true,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'isActive' => true,
            ],
            [
                'name' => 'Kasir',
                'email' => 'kasir@test.com',
                'password' => Hash::make('password'),
                'role' => 'KASIR',
                'isActive' => true,
            ],
            [
                'name' => 'Developer',
                'email' => 'developer@test.com',
                'password' => Hash::make('password'),
                'role' => 'DEVELOPER',
                'isActive' => true,
            ],
            [
                'name' => 'Supplier Test',
                'email' => 'supplier@test.com',
                'password' => Hash::make('password'),
                'role' => 'SUPPLIER',
                'isActive' => true,
            ],
            [
                'name' => 'Member User',
                'email' => 'user@test.com',
                'password' => Hash::make('password'),
                'role' => 'USER',
                'isActive' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Created 6 users (one per role)');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['SUPER_ADMIN', 'superadmin@test.com', 'password'],
                ['ADMIN', 'admin@test.com', 'password'],
                ['KASIR', 'kasir@test.com', 'password'],
                ['DEVELOPER', 'developer@test.com', 'password'],
                ['SUPPLIER', 'supplier@test.com', 'password'],
                ['USER', 'user@test.com', 'password'],
            ]
        );
    }
}
