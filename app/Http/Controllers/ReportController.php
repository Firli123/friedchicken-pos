<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $transactions = Transaction::with(['items', 'user'])
            ->paid()
            ->whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCount   = $transactions->count();
        $totalCash    = $transactions->where('payment_method', 'cash')->sum('total');
        $totalQris    = $transactions->where('payment_method', 'qris')->sum('total');

        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('transaction', fn ($q) => $q->paid()->whereDate('created_at', $date))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('reports.daily', compact(
            'date', 'transactions', 'totalRevenue', 'totalCount',
            'totalCash', 'totalQris', 'topProducts'
        ));
    }

    public function monthly(Request $request)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month . '-01')
            : Carbon::now()->startOfMonth();

        $transactions = Transaction::paid()
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCount   = $transactions->count();
        $totalCash    = $transactions->where('payment_method', 'cash')->sum('total');
        $totalQris    = $transactions->where('payment_method', 'qris')->sum('total');

        // Daily breakdown for chart
        $daysInMonth = $month->daysInMonth;
        $dailyData   = collect();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayDate = $month->copy()->day($d);
            $dailyData->push([
                'label'   => $d,
                'revenue' => Transaction::paid()
                    ->whereDate('created_at', $dayDate)
                    ->sum('total'),
            ]);
        }

        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('transaction', fn ($q) => $q->paid()
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('reports.monthly', compact(
            'month', 'transactions', 'totalRevenue', 'totalCount',
            'totalCash', 'totalQris', 'dailyData', 'topProducts'
        ));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        if ($type === 'monthly') {
            $date = $request->filled('month')
                ? Carbon::parse($request->month . '-01')
                : Carbon::now()->startOfMonth();
        }

        $transactions = Transaction::with(['items', 'user'])
            ->paid()
            ->when($type === 'daily', fn ($q) => $q->whereDate('created_at', $date))
            ->when($type === 'monthly', fn ($q) => $q
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year))
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCash    = $transactions->where('payment_method', 'cash')->sum('total');
        $totalQris    = $transactions->where('payment_method', 'qris')->sum('total');

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions', 'totalRevenue', 'totalCash', 'totalQris', 'date', 'type'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-' . ($type === 'daily' ? $date->format('Y-m-d') : $date->format('Y-m')) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        if ($type === 'monthly') {
            $date = $request->filled('month')
                ? Carbon::parse($request->month . '-01')
                : Carbon::now()->startOfMonth();
        }

        $filename = 'laporan-' . ($type === 'daily' ? $date->format('Y-m-d') : $date->format('Y-m')) . '.xlsx';

        return Excel::download(new ReportExport($date, $type), $filename);
    }
}
