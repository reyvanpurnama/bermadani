<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\POS;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect('/admin');
});

// POS Route - Protected by auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/pos', POS::class)->name('pos');
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
});
