<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Carbon;

class ReportExport
{
    public function __construct(
        private Carbon $date,
        private string $type = 'daily'
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $transactions = Transaction::with(['items', 'user'])
            ->paid()
            ->when($this->type === 'daily', fn($q) => $q->whereDate('created_at', $this->date))
            ->when($this->type === 'monthly', fn($q) => $q
                ->whereMonth('created_at', $this->date->month)
                ->whereYear('created_at', $this->date->year))
            ->orderByDesc('created_at')
            ->get();

        $label = $this->type === 'daily'
            ? $this->date->format('d/m/Y')
            : $this->date->format('F Y');

        $filename = 'laporan-' . ($this->type === 'daily'
            ? $this->date->format('Y-m-d')
            : $this->date->format('Y-m')) . '.csv';

        $totalRevenue = $transactions->sum('total');
        $totalCash    = $transactions->where('payment_method', 'cash')->sum('total');
        $totalQris    = $transactions->where('payment_method', 'qris')->sum('total');
        $totalCount   = $transactions->count();

        $type = $this->type;

        return response()->stream(function () use (
            $transactions, $label, $totalRevenue,
            $totalCash, $totalQris, $totalCount, $type
        ) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['FRIED CHICKEN POS - Laporan ' . ucfirst($type) . ' ' . $label], ';');
            fputcsv($handle, [''], ';');
            fputcsv($handle, ['RINGKASAN'], ';');
            fputcsv($handle, ['Total Pendapatan', 'Rp' . number_format($totalRevenue, 0, ',', '.')], ';');
            fputcsv($handle, ['Total Transaksi', $totalCount . ' transaksi'], ';');
            fputcsv($handle, ['Pembayaran Cash', 'Rp' . number_format($totalCash, 0, ',', '.')], ';');
            fputcsv($handle, ['Pembayaran QRIS', 'Rp' . number_format($totalQris, 0, ',', '.')], ';');
            fputcsv($handle, [''], ';');

            fputcsv($handle, [
                'No. Transaksi', 'Tanggal', 'Jam', 'Kasir',
                'Item Pesanan', 'Subtotal', 'Diskon', 'Pajak',
                'Total', 'Metode Bayar', 'Dibayar', 'Kembalian', 'Status',
            ], ';');

            foreach ($transactions as $trx) {
                $items = $trx->items
                    ->map(fn($i) => $i->product_name . ' x' . $i->quantity)
                    ->join(', ');

                fputcsv($handle, [
                    $trx->number,
                    $trx->created_at->format('d/m/Y'),
                    $trx->created_at->format('H:i:s'),
                    $trx->user->name,
                    $items,
                    $trx->subtotal,
                    $trx->discount,
                    $trx->tax,
                    $trx->total,
                    strtoupper($trx->payment_method),
                    $trx->amount_paid,
                    $trx->change_amount,
                    ucfirst($trx->payment_status),
                ], ';');
            }

            fputcsv($handle, [''], ';');
            fputcsv($handle, [
                'TOTAL', '', '', '', '',
                $transactions->sum('subtotal'),
                '', '', $totalRevenue,
                '', '', '', '',
            ], ';');

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}