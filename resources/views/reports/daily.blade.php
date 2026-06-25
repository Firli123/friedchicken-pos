@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-700 mb-0">Laporan Harian</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.pdf', ['type'=>'daily', 'date'=>$date->format('Y-m-d')]) }}"
           class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.export.excel', ['type'=>'daily', 'date'=>$date->format('Y-m-d')]) }}"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

{{-- Date Picker --}}
<div class="pos-card p-3 mb-4">
    <form method="GET" class="d-flex align-items-center gap-3">
        <label class="fw-600" style="font-size:0.85rem;white-space:nowrap;">Pilih Tanggal:</label>
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
               class="form-control" style="max-width:200px;" onchange="this.form.submit()">
        <span style="font-size:0.85rem;color:#757575;">
            {{ $date->translatedFormat('l, d F Y') }}
        </span>
    </form>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Total Pendapatan</div>
            <div class="stat-value text-danger">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Jumlah Transaksi</div>
            <div class="stat-value" style="color:#1565C0;">{{ $totalCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Pembayaran Cash</div>
            <div class="stat-value" style="color:#2E7D32;font-size:1.3rem;">Rp{{ number_format($totalCash, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Pembayaran QRIS</div>
            <div class="stat-value" style="color:#7B1FA2;font-size:1.3rem;">Rp{{ number_format($totalQris, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Top Products --}}
    <div class="col-md-5">
        <div class="pos-card p-4 h-100">
            <h6 class="fw-700 mb-3">Produk Terlaris</h6>
            @if($topProducts->isEmpty())
            <div class="text-center text-muted py-4">Tidak ada data</div>
            @else
            <table class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Pendapatan</th></tr></thead>
                <tbody>
                    @foreach($topProducts as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td class="fw-600" style="font-size:0.85rem;">{{ $p->product_name }}</td>
                        <td class="text-end fw-700 text-danger">{{ $p->total_qty }}</td>
                        <td class="text-end" style="font-size:0.82rem;">Rp{{ number_format($p->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- Transactions list --}}
    <div class="col-md-7">
        <div class="pos-card p-4">
            <h6 class="fw-700 mb-3">Daftar Transaksi ({{ $totalCount }})</h6>
            <div style="max-height:320px;overflow-y:auto;">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Jam</th>
                            <th>Kasir</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td><a href="{{ route('transactions.show', $trx) }}" class="text-danger text-decoration-none fw-600" style="font-size:0.82rem;">{{ $trx->number }}</a></td>
                            <td style="font-size:0.8rem;color:#757575;">{{ $trx->created_at->format('H:i') }}</td>
                            <td style="font-size:0.82rem;">{{ $trx->user->name }}</td>
                            <td class="text-end fw-600" style="font-size:0.85rem;">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->payment_method === 'cash')
                                <span class="badge" style="background:#E8F5E9;color:#2E7D32;font-size:0.7rem;">Cash</span>
                                @else
                                <span class="badge" style="background:#F3E5F5;color:#7B1FA2;font-size:0.7rem;">QRIS</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada transaksi pada tanggal ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
