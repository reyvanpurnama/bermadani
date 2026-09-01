<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppIsInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip installer check for install routes, assets, and health check
        if ($request->is('install*') || $request->is('up')) {
            return $next($request);
        }

        // If storage/installed lock file does NOT exist, redirect to installer
        if (!file_exists(storage_path('installed'))) {
            return redirect()->route('installer.step1');
        }

        return $next($request);
    }
}
