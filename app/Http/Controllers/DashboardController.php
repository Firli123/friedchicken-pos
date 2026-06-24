<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // === Statistik Hari Ini ===
        $todayRevenue = Transaction::paid()->today()->sum('total');
        $todayCount   = Transaction::paid()->today()->count();
        $todayCash    = Transaction::paid()->today()->where('payment_method', 'cash')->sum('total');
        $todayQris    = Transaction::paid()->today()->where('payment_method', 'qris')->sum('total');

        // === Statistik Bulan Ini ===
        $monthRevenue = Transaction::paid()->thisMonth()->sum('total');

        // === Produk Terlaris Hari Ini ===
        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('transaction', fn ($q) => $q->paid()->today())
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // === Grafik Penjualan 7 Hari Terakhir ===
        $dailyChart = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i);
            $revenue = Transaction::paid()
                ->whereDate('created_at', $date)
                ->sum('total');
            $dailyChart->push([
                'label'   => $date->format('d/m'),
                'revenue' => $revenue,
            ]);
        }

        // === Grafik Penjualan 4 Minggu Terakhir ===
        $weeklyChart = collect();
        for ($i = 3; $i >= 0; $i--) {
            $start   = Carbon::now()->startOfWeek()->subWeeks($i);
            $end     = (clone $start)->endOfWeek();
            $revenue = Transaction::paid()
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');
            $weeklyChart->push([
                'label'   => 'Minggu ' . $start->format('d/m'),
                'revenue' => $revenue,
            ]);
        }

        // === Grafik Penjualan 6 Bulan Terakhir ===
        $monthlyChart = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month   = Carbon::now()->subMonths($i);
            $revenue = Transaction::paid()
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total');
            $monthlyChart->push([
                'label'   => $month->translatedFormat('M Y'),
                'revenue' => $revenue,
            ]);
        }

        // === Transaksi Terbaru ===
        $recentTransactions = Transaction::with('user')
            ->paid()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'todayRevenue', 'todayCount', 'todayCash', 'todayQris',
            'monthRevenue', 'topProducts', 'dailyChart', 'weeklyChart',
            'monthlyChart', 'recentTransactions'
        ));
    }
}
