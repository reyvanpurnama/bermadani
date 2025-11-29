<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan',
                'description' => 'Produk makanan ringan, berat, dan camilan',
                'icon' => '🍔',
                'order' => 1,
                'isActive' => true,
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman kemasan, soft drink, dan air mineral',
                'icon' => '🥤',
                'order' => 2,
                'isActive' => true,
            ],
            [
                'name' => 'Alat Tulis',
                'description' => 'Perlengkapan tulis menulis dan kantor',
                'icon' => '✏️',
                'order' => 3,
                'isActive' => true,
            ],
            [
                'name' => 'Kebersihan',
                'description' => 'Produk kebersihan dan sanitasi',
                'icon' => '🧼',
                'order' => 4,
                'isActive' => true,
            ],
            [
                'name' => 'Rokok',
                'description' => 'Produk rokok dan tembakau',
                'icon' => '🚬',
                'order' => 5,
                'isActive' => true,
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Peralatan elektronik dan aksesoris',
                'icon' => '🔌',
                'order' => 6,
                'isActive' => true,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Produk lain-lain',
                'icon' => '📦',
                'order' => 99,
                'isActive' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
