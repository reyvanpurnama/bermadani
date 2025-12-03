<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.com',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'isActive' => true,
        ]);

        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@koperasi.com',
            'password' => bcrypt('password'),
            'role' => 'SUPER_ADMIN',
            'isActive' => true,
        ]);

        echo "✅ Admin users created!\n";
        echo "   - admin@koperasi.com / password\n";
        echo "   - superadmin@koperasi.com / password\n";
    }
}
