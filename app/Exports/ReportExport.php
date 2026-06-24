<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    public function __construct(
        private Carbon $date,
        private string $type = 'daily'
    ) {}

    public function collection()
    {
        return Transaction::with(['items', 'user'])
            ->paid()
            ->when($this->type === 'daily', fn ($q) => $q->whereDate('created_at', $this->date))
            ->when($this->type === 'monthly', fn ($q) => $q
                ->whereMonth('created_at', $this->date->month)
                ->whereYear('created_at', $this->date->year))
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'Jam',
            'Kasir',
            'Item',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total',
            'Metode Bayar',
            'Jumlah Bayar',
            'Kembalian',
            'Status',
        ];
    }

    public function map($transaction): array
    {
        $items = $transaction->items
            ->map(fn ($i) => $i->product_name . ' x' . $i->quantity)
            ->join(', ');

        return [
            $transaction->number,
            $transaction->created_at->format('d/m/Y'),
            $transaction->created_at->format('H:i:s'),
            $transaction->user->name,
            $items,
            $transaction->subtotal,
            $transaction->discount,
            $transaction->tax,
            $transaction->total,
            strtoupper($transaction->payment_method),
            $transaction->amount_paid,
            $transaction->change_amount,
            ucfirst($transaction->payment_status),
        ];
    }

    public function title(): string
    {
        return $this->type === 'daily'
            ? 'Laporan ' . $this->date->format('d-m-Y')
            : 'Laporan ' . $this->date->format('m-Y');
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E53935']]],
        ];
    }
}
