<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'No member profile found.');
        }

        // Get transactions for this member
        $transactions = Transaction::where('memberId', $member->id)
            ->completed()
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Stats
        $totalSpent = Transaction::where('memberId', $member->id)
            ->completed()
            ->sum('totalAmount');

        $totalTransactions = Transaction::where('memberId', $member->id)
            ->completed()
            ->count();

        return view('member.transactions.index', [
            'member' => $member,
            'transactions' => $transactions,
            'totalSpent' => $totalSpent,
            'totalTransactions' => $totalTransactions,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member;

        $transaction = Transaction::with('items')
            ->where('memberId', $member->id)
            ->findOrFail($id);

        return view('member.transactions.show', [
            'transaction' => $transaction,
            'member' => $member,
        ]);
    }
}
