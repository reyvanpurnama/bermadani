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
        // Seed categories, products, role users, and Tira Setyani member data
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            RoleUsersSeeder::class,
            TiraSeeder::class,
        ]);
    }
}
