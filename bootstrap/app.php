<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Dynamic role-based redirect for authenticated users away from guest routes
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if (!$user) return '/login';
            return match ($user->role) {
                'SUPER_ADMIN', 'ADMIN', 'DEVELOPER' => '/admin',
                'KASIR' => '/admin/pos',
                'SUPPLIER' => '/supplier',
                'MEMBER' => '/member/dashboard',
                default => '/login',
            };
        });

        // Register custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'supplier.status' => \App\Http\Middleware\CheckSupplierStatus::class,
            'cashier.shift' => \App\Http\Middleware\CheckCashierShift::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
            'member.type' => \App\Http\Middleware\CheckMemberType::class,
            'installed' => \App\Http\Middleware\EnsureAppIsInstalled::class,
            'uninstalled' => \App\Http\Middleware\RedirectIfInstalled::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureAppIsInstalled::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
