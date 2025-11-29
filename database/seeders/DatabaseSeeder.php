<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@koperasiumb.com',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        // Create kasir user
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@koperasiumb.com',
            'password' => bcrypt('password'),
            'role' => 'KASIR',
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }
}
