<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // If storage/installed lock file exists, prevent accessing installer
        if (file_exists(storage_path('installed'))) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
