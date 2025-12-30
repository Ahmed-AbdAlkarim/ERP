<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class CashboxTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('wallet.cashbox')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.cashboxes.transactions.index', compact('transactions'));
    }
}
