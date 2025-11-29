<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

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
            return redirect()->intended('/admin');
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    });
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
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
    Route::get('/categories', function () {
        return view('admin.placeholder', ['title' => 'Kategori']);
    })->name('admin.categories');
    
    // Members
    Route::get('/members', function () {
        return view('admin.placeholder', ['title' => 'Anggota']);
    })->name('admin.members');
    
    // Transactions
    Route::get('/transactions', function () {
        return view('admin.placeholder', ['title' => 'Transaksi']);
    })->name('admin.transactions');
    
    // Savings
    Route::get('/savings', function () {
        return view('admin.placeholder', ['title' => 'Simpanan']);
    })->name('admin.savings');
    
    // Loans
    Route::get('/loans', function () {
        return view('admin.placeholder', ['title' => 'Pinjaman']);
    })->name('admin.loans');
    
    // Users (Admin only)
    Route::get('/users', function () {
        return view('admin.placeholder', ['title' => 'Pengguna']);
    })->name('admin.users');
    
    // Suppliers
    Route::get('/suppliers', function () {
        return view('admin.placeholder', ['title' => 'Supplier']);
    })->name('admin.suppliers');
    
    // Settings
    Route::get('/settings', function () {
        return view('admin.placeholder', ['title' => 'Pengaturan']);
    })->name('admin.settings');
    
    // Receipt
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
});

// Keep old /pos route for backward compatibility
Route::middleware(['auth'])->get('/pos', function () {
    return redirect()->route('admin.pos');
});
