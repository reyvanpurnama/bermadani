<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share pending products count to sidebar
        View::composer('partials.sidebar', function ($view) {
            $pendingCount = cache()->remember('pending_products_count', 300, function () {
                return Product::where('approvalStatus', 'PENDING')->count();
            });
            
            $view->with('pendingProductsCount', $pendingCount);
        });
    }
}
