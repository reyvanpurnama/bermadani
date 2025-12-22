<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * Usage: Route::middleware('role:ADMIN,SUPER_ADMIN')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        
        // Check if user is active
        if (method_exists($user, 'isActive') && !$user->isActive) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact administrator.'
            ]);
        }
        
        // If no specific roles required, just check authentication
        if (empty($roles)) {
            return $next($request);
        }
        
        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Access denied. Required role: ' . implode(' or ', $roles));
        }
        
        return $next($request);
    }
}
