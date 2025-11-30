<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Models\Supplier;

class SupplierAuthController extends Controller
{
    /**
     * Show supplier login form
     */
    public function showLoginForm()
    {
        return view('supplier.auth.login');
    }

    /**
     * Handle supplier login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if supplier exists and is active
        $supplier = Supplier::where('email', $credentials['email'])->first();

        if (!$supplier) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar sebagai supplier.',
            ])->onlyInput('email');
        }

        // Check supplier status
        if ($supplier->status === 'PENDING') {
            return back()->withErrors([
                'email' => 'Akun Anda masih menunggu persetujuan admin.',
            ])->onlyInput('email');
        }

        if ($supplier->status === 'REJECTED') {
            return back()->withErrors([
                'email' => 'Pendaftaran Anda ditolak. Alasan: ' . $supplier->rejectedReason,
            ])->onlyInput('email');
        }

        if ($supplier->status === 'SUSPENDED' || $supplier->isSuspendedForPayment) {
            return back()->withErrors([
                'email' => 'Akun Anda di-suspend. ' . ($supplier->suspensionReason ? 'Alasan: ' . $supplier->suspensionReason : ''),
            ])->onlyInput('email');
        }

        if (!$supplier->isActive) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi admin.',
            ])->onlyInput('email');
        }

        // Attempt login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if user is actually a supplier
            if ($user->role !== 'SUPPLIER') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini bukan akun supplier.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Update last login
            $user->updateLastLogin();

            // Log activity
            ActivityLog::create([
                'userId' => $user->id,
                'module' => 'Auth',
                'action' => 'LOGIN',
                'description' => 'Supplier login: ' . $user->email,
                'ipAddress' => $request->ip(),
                'userAgent' => $request->userAgent(),
            ]);

            return redirect()->route('supplier.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle supplier logout
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        if (Auth::check()) {
            ActivityLog::create([
                'userId' => Auth::id(),
                'module' => 'Auth',
                'action' => 'LOGOUT',
                'description' => 'Supplier logout: ' . Auth::user()->email,
                'ipAddress' => $request->ip(),
                'userAgent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supplier.login')->with('success', 'Anda berhasil logout.');
    }
}
