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

        // Create users for production
        $users = [
            [
                'name' => 'Ridlo Abdillah',
                'email' => 'ridloabdillah@bermadaniumbandung.id',
                'password' => Hash::make('password'),
                'role' => 'SUPER_ADMIN',
                'isActive' => true,
            ],
            [
                'name' => 'Developer',
                'email' => 'bermadani@dev.com',
                'password' => Hash::make('password'),
                'role' => 'DEVELOPER',
                'isActive' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Created 2 users');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['SUPER_ADMIN', 'ridloabdillah@bermadaniumbandung.id', 'password'],
                ['DEVELOPER', 'bermadani@dev.com', 'password'],
            ]
        );
    }
}
