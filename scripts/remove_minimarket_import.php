<?php
/**
 * Script hapus produk import Minimarket
 * Jalankan: php artisan tinker scripts/remove_minimarket_import.php
 * Atau: php scripts/remove_minimarket_import.php
 */

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;

$category = Category::where('name', 'Minimarket')->first();

if (!$category) {
    echo "Category 'Minimarket' tidak ditemukan.\n";
    return;
}

$products = Product::where('category_id', $category->id)->get();

if ($products->isEmpty()) {
    echo "Tidak ada produk dengan category Minimarket.\n";
    return;
}

echo "Kategori: {$category->name} (ID: {$category->id})\n";
echo "Produk ditemukan: " . $products->count() . "\n\n";

// Hapus stock movements dulu
$productIds = $products->pluck('id')->toArray();
$stockDeleted = StockMovement::whereIn('product_id', $productIds)->delete();
echo "Stock movements dihapus: {$stockDeleted}\n";

// Hapus produk
$productDeleted = Product::whereIn('id', $productIds)->delete();
echo "Produk dihapus: {$productDeleted}\n";

// Hapus category kalau mau
// Category::where('name', 'Minimarket')->delete();
// echo "Category Minimarket dihapus.\n";

echo "\nSelesai! Import sudah dibatalkan.\n";