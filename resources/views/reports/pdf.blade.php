<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        body { margin: 0; padding: 0; color: #212121; }

        .header { background: #E53935; color: #fff; padding: 16px 20px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 4px 0 0; font-size: 10px; opacity: 0.85; }

        .stats-row { display: flex; gap: 12px; margin: 0 20px 16px; }
        .stat-box { flex: 1; border: 1px solid #E0E0E0; border-radius: 6px; padding: 10px; text-align: center; }
        .stat-box .label { font-size: 9px; color: #757575; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-box .value { font-size: 14px; font-weight: bold; color: #E53935; margin-top: 4px; }

        table { width: calc(100% - 40px); margin: 0 20px 16px; border-collapse: collapse; }
        thead th { background: #E53935; color: #fff; padding: 7px 8px; text-align: left; font-weight: bold; font-size: 10px; }
        tbody tr:nth-child(even) { background: #FFF5F5; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #F0F0F0; font-size: 10px; }
        tfoot td { padding: 6px 8px; font-weight: bold; border-top: 2px solid #E53935; }

        .text-right { text-align: right; }
        .footer { margin-top: 20px; padding: 10px 20px; border-top: 1px solid #E0E0E0; font-size: 9px; color: #757575; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('store_name', 'FRIED CHICKEN') }}</h1>
        <p>
            Laporan {{ $type === 'daily' ? 'Harian — ' . $date->format('d/m/Y') : 'Bulanan — ' . $date->format('F Y') }}
            &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Transaksi</div>
            <div class="value" style="color:#1565C0;">{{ $transactions->count() }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Cash</div>
            <div class="value" style="color:#2E7D32;">Rp{{ number_format($totalCash, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="label">QRIS</div>
            <div class="value" style="color:#7B1FA2;">Rp{{ number_format($totalQris, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal & Jam</th>
                <th>Kasir</th>
                <th>Item</th>
                <th class="text-right">Total</th>
                <th>Metode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ $trx->number }}</td>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $trx->user->name }}</td>
                <td>
                    @foreach($trx->items as $item)
                        {{ $item->product_name }} x{{ $item->quantity }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="text-right">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                <td>{{ strtoupper($trx->payment_method) }}</td>
                <td>{{ ucfirst($trx->payment_status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Laporan ini digenerate otomatis oleh sistem Fried Chicken POS &middot; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
