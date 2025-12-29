<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Supplier Registration (Public)
Route::get('/daftar-supplier', function () {
    return view('supplier.register');
})->name('supplier.register');

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
            if ($user->isMember()) {
                return redirect()->route('member.dashboard');
            }
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
    
    Route::get('/transaksi/manual/riwayat', function () {
        return view('admin.manual-transaction-history');
    })->name('admin.manual-transaction.history');
    
    Route::get('/transaksi/manual/{id}', function ($id) {
        return view('admin.manual-transaction-detail', ['transactionId' => $id]);
    })->name('admin.manual-transaction.detail');
    
    Route::get('/transaksi/{id}', function ($id) {
        return view('admin.transactions.detail', ['transactionId' => $id]);
    })->name('transaksi.detail');
    
    // Members Management
    Route::prefix('members')->name('admin.members.')->group(function () {
        Route::get('/', function () {
            return view('admin.members.index');
        })->name('index');
        
        Route::get('/create', function () {
            return view('admin.members.create');
        })->name('create');
        
        Route::get('/{member}', function ($member) {
            $member = \App\Models\Member::findOrFail($member);
            return view('admin.members.show', compact('member'));
        })->name('show');
        
        Route::get('/{member}/edit', function ($member) {
            $member = \App\Models\Member::findOrFail($member);
            return view('admin.members.edit', compact('member'));
        })->name('edit');
        
        Route::get('/{member}/simpanan', function ($member) {
            $member = \App\Models\Member::findOrFail($member);
            return view('admin.members.simpanan', compact('member'));
        })->name('simpanan');
    });
    
    // Savings
    Route::get('/savings', function () {
        return view('admin.placeholder', ['title' => 'Simpanan']);
    })->name('admin.savings');
    
    // Payments - Pembayaran Simpanan
    Route::prefix('payments')->name('admin.payments.')->group(function () {
        Route::get('/create', \App\Livewire\Admin\PaymentForm::class)->name('create');
        Route::get('/receipt/{receiptNumber}', \App\Livewire\Admin\PaymentReceipt::class)->name('receipt');
    });
    
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
    
    // Suppliers
    Route::get('/suppliers', function () {
        return view('admin.placeholder', ['title' => 'Supplier']);
    })->name('admin.suppliers');
    
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

// Member Portal Routes
Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', \App\Livewire\Member\Dashboard::class)->name('dashboard');
    Route::get('/profile', \App\Livewire\Member\Profile::class)->name('profile');
    Route::get('/simpanan', \App\Livewire\Member\Simpanan::class)->name('simpanan');
    Route::get('/transactions', \App\Livewire\Member\Transactions::class)->name('transactions');
});
