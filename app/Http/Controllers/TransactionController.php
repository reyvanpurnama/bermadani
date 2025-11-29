<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items.product', 'member']);

        return view('transactions.receipt', [
            'transaction' => $transaction,
        ]);
    }
}
