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

        // Coba login sebagai User (admin/kasir) dulu
        if (Auth::attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Update last login timestamp
            $user->updateLastLogin();

            // Log login activity
            ActivityLog::logLogin();

            // Redirect based on role
            if ($user->isMember()) {
                return redirect()->route('member.dashboard');
            }

            if ($user->isKasir()) {
                return redirect()->route('kasir.dashboard');
            }

            return redirect()->intended('/admin');
        }

        // Jika gagal, coba login sebagai Supplier menggunakan guard supplier
        if (Auth::guard('supplier')->attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();

            /** @var \App\Models\Supplier $supplier */
            $supplier = Auth::guard('supplier')->user();

            // Update last login timestamp
            $supplier->updateLastLogin();

            // Log login activity
            ActivityLog::log(
                'login',
                'Supplier Login',
                'Supplier ' . $supplier->businessName . ' logged in',
                $supplier
            );

            // Check status supplier
            if (in_array($supplier->status, ['PENDING', 'REJECTED'])) {
                return redirect()->route('supplier.pending');
            }

            // Jika status APPROVED/ACTIVE, redirect ke dashboard supplier
            return redirect()->route('supplier.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    });
});

Route::post('/logout', function () {
    // Log logout activity before logout
    ActivityLog::logLogout();

    // Logout from all guards
    Auth::guard('web')->logout();
    Auth::guard('supplier')->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// Supplier Pending Page (ketika supplier belum approve)
Route::middleware('auth:supplier')->get('/supplier/pending', [SupplierController::class, 'pending'])->name('supplier.pending');

// Supplier Portal Routes - Protected (Login via /login)
Route::middleware(['auth:supplier', 'supplier.status', 'log.activity'])->prefix('supplier')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\SupplierDashboardController::class, 'index'])->name('supplier.dashboard');

    // Product Management
    Route::get('/products', [App\Http\Controllers\Supplier\SupplierProductController::class, 'index'])->name('supplier.products.index');
    Route::get('/products/create', [App\Http\Controllers\Supplier\SupplierProductController::class, 'create'])->name('supplier.products.create');
    Route::post('/products', [App\Http\Controllers\Supplier\SupplierProductController::class, 'store'])->name('supplier.products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Supplier\SupplierProductController::class, 'edit'])->name('supplier.products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Supplier\SupplierProductController::class, 'update'])->name('supplier.products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Supplier\SupplierProductController::class, 'destroy'])->name('supplier.products.destroy');

    Route::get('/sales', [App\Http\Controllers\Supplier\SupplierSalesController::class, 'index'])->name('supplier.sales');

    Route::get('/restock', function () {
        return view('supplier.restock');
    })->name('supplier.restock');

    Route::get('/profile', function () {
        return view('supplier.profile');
    })->name('supplier.profile');
});

// Admin Routes - Protected
Route::middleware(['auth', 'role:SUPER_ADMIN,ADMIN,DEVELOPER', 'log.activity'])->prefix('admin')->group(function () {
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


    Route::get('/restock-requests', \App\Livewire\Admin\RestockManagement::class)->name('admin.restock-requests');
    Route::post('/inventaris', [App\Http\Controllers\ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/inventaris/mutasi', \App\Livewire\Admin\StockMutation::class)->name('admin.stock-mutation');
    Route::get('/inventaris/penyesuaian', \App\Livewire\Admin\StockAdjustment::class)->name('admin.stock-adjustment');
    Route::get('/inventaris/review', \App\Livewire\Admin\ProductReview::class)->name('admin.product-review');
    Route::get('/inventaris/{id}/edit', function ($id) {
        return view('admin.products.edit', ['productId' => $id]);
    })->name('admin.products.edit');
    Route::put('/inventaris/{id}', [App\Http\Controllers\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/inventaris/{id}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Konsinyasi
    Route::get('/konsinyasi/batch', \App\Livewire\Admin\ConsignmentBatches::class)->name('admin.consignment-batches');

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
    Route::middleware(['role:SUPER_ADMIN,DEVELOPER,ADMIN'])->get('/users', function () {
        return view('admin.users.index');
    })->name('admin.users');

    // Suppliers Management
    Route::get('/suppliers', function () {
        return view('admin.suppliers.index');
    })->name('admin.suppliers');
    Route::get('/suppliers/{id}', function ($id) {
        return view('admin.suppliers.detail', ['supplierId' => $id]);
    })->name('admin.suppliers.detail');
    // Payment verification routes
    Route::post('/suppliers/{id}/verify-payment', [SupplierController::class, 'verifyPayment'])->name('admin.suppliers.verifyPayment');
    Route::post('/suppliers/{id}/reject-payment', [SupplierController::class, 'rejectPayment'])->name('admin.suppliers.rejectPayment');

    // Supplier approval routes
    Route::post('/suppliers/{id}/approve', [SupplierController::class, 'approve'])->name('admin.suppliers.approve');
    Route::post('/suppliers/{id}/reject', [SupplierController::class, 'reject'])->name('admin.suppliers.reject');
    Route::post('/suppliers/{id}/suspend', [SupplierController::class, 'suspend'])->name('admin.suppliers.suspend');
    Route::post('/suppliers/{id}/activate', [SupplierController::class, 'activate'])->name('admin.suppliers.activate');

    // Settings
    Route::get('/settings', function () {
        return view('admin.placeholder', ['title' => 'Pengaturan']);
    })->name('admin.settings');

    // Activity Logs (Admin, Super Admin, Developer)
    Route::middleware(['role:SUPER_ADMIN,DEVELOPER,ADMIN'])->get('/activity-logs', function () {
        return view('admin.activity-logs');
    })->name('admin.activity-logs');

    // Kasir History (Admin only)
    Route::middleware(['role:SUPER_ADMIN,ADMIN,DEVELOPER'])->get('/kasir-history', \App\Livewire\Admin\KasirHistory::class)->name('admin.kasir-history');

    // Monthly Financial Report
    Route::get('/reports/monthly-financial', \App\Livewire\Admin\MonthlyFinancialReport::class)->name('admin.reports.monthly-financial');

    // Balance Sheet (Neraca)
    Route::get('/reports/balance-sheet', \App\Livewire\Admin\Reports\BalanceSheet::class)->name('admin.reports.balance-sheet');

    // Developer Payroll (Admin/SuperAdmin only)
    Route::middleware(['role:SUPER_ADMIN,ADMIN'])->get('/developer-payroll', \App\Livewire\Admin\DeveloperPayroll::class)->name('admin.developer-payroll');

    // Developer Work Logs (Developer only)
    Route::middleware(['role:DEVELOPER'])->get('/work-logs', \App\Livewire\Developer\WorkLogManager::class)->name('developer.work-logs');


    // Receipt
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
});

// Kasir Routes - Protected
Route::middleware(['auth', 'role:KASIR', 'log.activity'])->prefix('kasir')->group(function () {
    // Kasir Dashboard
    Route::get('/', function () {
        return view('kasir.dashboard');
    })->name('kasir.dashboard');

    // POS Access for Kasir (requires active shift)
    Route::middleware(['cashier.shift'])->get('/pos', function () {
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
    Route::get('/transfer', \App\Livewire\Member\Transfer::class)->name('transfer');
    Route::get('/transfer/history', \App\Livewire\Member\TransferHistory::class)->name('transfer.history');
});
