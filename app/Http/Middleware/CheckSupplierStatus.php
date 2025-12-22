<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSupplierStatus
{
    /**
     * Handle an incoming request.
     * Verify that supplier is approved and active
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supplier = Auth::guard('supplier')->user();
        
        if (!$supplier) {
            return redirect()->route('login')->withErrors([
                'email' => 'Please login to continue.'
            ]);
        }
        
        // Check if supplier is pending or rejected
        if (in_array($supplier->status, ['PENDING', 'REJECTED'])) {
            return redirect()->route('supplier.pending');
        }
        
        // Check if supplier is suspended
        if ($supplier->status === 'SUSPENDED') {
            Auth::guard('supplier')->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Reason: ' . ($supplier->suspensionReason ?? 'Contact administrator for details.')
            ]);
        }
        
        // Check if supplier is active
        if (!$supplier->isActive || $supplier->status !== 'ACTIVE') {
            Auth::guard('supplier')->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is not active. Please contact administrator.'
            ]);
        }
        
        return $next($request);
    }
}
