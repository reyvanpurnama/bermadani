<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\CashierShift;

class CheckCashierShift
{
    /**
     * Handle an incoming request.
     * Verify that kasir has an active shift before accessing POS
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Only check for Kasir role
        if (!$user || !$user->isKasir()) {
            return $next($request);
        }
        
        // Check if kasir has active shift
        $activeShift = CashierShift::where('userId', $user->id)
            ->whereNull('endTime')
            ->first();
        
        if (!$activeShift) {
            return redirect()->route('kasir.dashboard')->withErrors([
                'shift' => 'You must start a shift before accessing POS. Please start your shift first.'
            ]);
        }
        
        // Attach shift to request for easy access in controllers
        $request->attributes->set('cashier_shift', $activeShift);
        
        return $next($request);
    }
}
