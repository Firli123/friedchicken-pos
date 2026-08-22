<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items'])
            ->orderByDesc('created_at');

        // Search
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter metode pembayaran
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Transaksi
        |--------------------------------------------------------------------------
        */

        $summaryQuery = clone $query;

        $totalTransaksi = (clone $summaryQuery)->count();

        $totalPendapatan = (clone $summaryQuery)->sum('total');

        $totalCash = (clone $summaryQuery)
    ->where('payment_method', 'cash')
    ->sum('total');

$totalQris = (clone $summaryQuery)
    ->where('payment_method', 'qris')
    ->sum('total');

$cashCount = (clone $summaryQuery)
    ->where('payment_method', 'cash')
    ->count();

$qrisCount = (clone $summaryQuery)
    ->where('payment_method', 'qris')
    ->count();
        /*
        |--------------------------------------------------------------------------
        | Data Tabel
        |--------------------------------------------------------------------------
        */

        $transactions = $query
            ->paginate(20)
            ->withQueryString();

       return view('transactions.index', compact(
    'transactions',
    'totalTransaksi',
    'totalPendapatan',
    'totalCash',
    'totalQris',
    'cashCount',
    'qrisCount'
));
       
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['items', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->payment_status === 'paid') {
            return back()->with('error', 'Transaksi yang sudah lunas tidak dapat dibatalkan.');
        }

        $transaction->update([
            'payment_status' => 'cancelled'
        ]);

        return back()->with('success', "Transaksi {$transaction->number} dibatalkan.");
    }
}