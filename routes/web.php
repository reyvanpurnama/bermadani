<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Supplier Registration (Public)
Route::get('/daftar-supplier', [SupplierController::class, 'showRegistrationForm'])->name('supplier.register');
Route::post('/daftar-supplier', [SupplierController::class, 'register'])->name('supplier.register.store');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();
            
            // Update last login timestamp
            Auth::user()->updateLastLogin();
            
            // Log login activity
            ActivityLog::logLogin();
            
            // Redirect based on role
            $user = Auth::user();
            if ($user->isKasir()) {
                return redirect()->route('kasir.dashboard');
            }
            
            return redirect()->intended('/admin');
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    });
});

Route::post('/logout', function () {
    // Log logout activity before logout
    ActivityLog::logLogout();
    
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// Supplier Auth Routes
Route::middleware('guest')->prefix('supplier')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\SupplierAuthController::class, 'showLoginForm'])->name('supplier.login');
    Route::post('/login', [App\Http\Controllers\Auth\SupplierAuthController::class, 'login']);
});

// Supplier Portal Routes - Protected
Route::middleware(['auth'])->prefix('supplier')->group(function () {
    Route::get('/dashboard', function () {
        // Check if user is supplier
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.dashboard');
    })->name('supplier.dashboard');
    
    Route::get('/products', function () {
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.products.index');
    })->name('supplier.products');
    
    Route::get('/products/submit', function () {
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.products.submit');
    })->name('supplier.products.submit');
    
    Route::get('/sales', function () {
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.sales');
    })->name('supplier.sales');
    
    Route::get('/restock', function () {
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.restock');
    })->name('supplier.restock');
    
    Route::get('/profile', function () {
        if (auth()->user()->role !== 'SUPPLIER') {
            abort(403, 'Unauthorized');
        }
        return view('supplier.profile');
    })->name('supplier.profile');
    
    Route::post('/logout', [App\Http\Controllers\Auth\SupplierAuthController::class, 'logout'])->name('supplier.logout');
});

// Admin Routes - Protected
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // POS
    Route::get('/pos', function () {
        return view('admin.pos');
    })->name('admin.pos');
    
    // Inventaris / Products
    Route::get('/inventaris', function () {
        return view('admin.products.index');
    })->name('admin.products');
    Route::get('/inventaris/tambah', function () {
        return view('admin.products.create');
    })->name('admin.products.create');
    Route::post('/inventaris', [App\Http\Controllers\ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/inventaris/{id}/edit', function ($id) {
        return view('admin.products.edit', ['productId' => $id]);
    })->name('admin.products.edit');
    Route::put('/inventaris/{id}', [App\Http\Controllers\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/inventaris/{id}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('admin.products.destroy');
    
    // Categories
    Route::get('/kategori', function () {
        return view('admin.categories.index');
    })->name('admin.categories');
    
    // Transactions / Transaksi
    Route::get('/transaksi', function () {
        return view('admin.transactions.index');
    })->name('admin.transactions');
    
    // Manual Transaction (Income/Expense) - HARUS DI ATAS {id}
    Route::get('/transaksi/manual', function () {
        return view('admin.manual-transaction');
    })->name('admin.manual-transaction');
    
    Route::get('/transaksi/{id}', function ($id) {
        return view('admin.transactions.detail', ['transactionId' => $id]);
    })->name('transaksi.detail');
    
    // Members (not in MVP)
    Route::get('/members', function () {
        return view('admin.placeholder', ['title' => 'Anggota']);
    })->name('admin.members');
    
    // Savings
    Route::get('/savings', function () {
        return view('admin.placeholder', ['title' => 'Simpanan']);
    })->name('admin.savings');
    
    // Loans
    Route::get('/loans', function () {
        return view('admin.placeholder', ['title' => 'Pinjaman']);
    })->name('admin.loans');
    
    // Users Management (Super Admin & Developer only for CRUD, Admin read-only)
    Route::get('/users', function () {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isDeveloper() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('admin.users.index');
    })->name('admin.users');
    
    // Suppliers Management
    Route::get('/suppliers', function () {
        return view('admin.suppliers.index');
    })->name('admin.suppliers');
    Route::get('/suppliers/{id}', function ($id) {
        return view('admin.suppliers.detail', ['supplierId' => $id]);
    })->name('admin.suppliers.detail');
    Route::post('/suppliers/{id}/approve', [SupplierController::class, 'approve'])->name('admin.suppliers.approve');
    Route::post('/suppliers/{id}/reject', [SupplierController::class, 'reject'])->name('admin.suppliers.reject');
    Route::post('/suppliers/{id}/suspend', [SupplierController::class, 'suspend'])->name('admin.suppliers.suspend');
    Route::post('/suppliers/{id}/activate', [SupplierController::class, 'activate'])->name('admin.suppliers.activate');
    
    // Settings
    Route::get('/settings', function () {
        return view('admin.placeholder', ['title' => 'Pengaturan']);
    })->name('admin.settings');
    
    // Activity Logs (Admin, Super Admin, Developer)
    Route::get('/activity-logs', function () {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isDeveloper() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('admin.activity-logs');
    })->name('admin.activity-logs');
    
    // Receipt
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
});

// Kasir Routes - Protected
Route::middleware(['auth'])->prefix('kasir')->group(function () {
    // Kasir Dashboard
    Route::get('/', function () {
        return view('kasir.dashboard');
    })->name('kasir.dashboard');
    
    // POS Access for Kasir
    Route::get('/pos', function () {
        return view('admin.pos');
    })->name('kasir.pos');
    
    // My Transactions
    Route::get('/transaksi', function () {
        return view('admin.transactions.index');
    })->name('kasir.transactions');
    
    // Transaction Detail
    Route::get('/transaksi/{id}', function ($id) {
        return view('admin.transactions.detail', ['transactionId' => $id]);
    })->name('kasir.transaction.detail');
});

// Keep old /pos route for backward compatibility
Route::middleware(['auth'])->get('/pos', function () {
    return redirect()->route('admin.pos');
});
