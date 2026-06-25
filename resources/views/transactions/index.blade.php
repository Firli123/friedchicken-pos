@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Riwayat Transaksi</h5>
</div>

<div class="pos-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="search" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="🔍 Cari nomor transaksi...">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Lunas</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                <option value="failed" {{ request('status')==='failed'?'selected':'' }}>Gagal</option>
                <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="method" class="form-select">
                <option value="">Semua Metode</option>
                <option value="cash" {{ request('method')==='cash'?'selected':'' }}>Cash</option>
                <option value="qris" {{ request('method')==='qris'?'selected':'' }}>QRIS</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-danger w-100"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<div class="pos-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal & Jam</th>
                    <th>Kasir</th>
                    <th>Item</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <a href="{{ route('transactions.show', $trx) }}"
                           class="fw-bold text-decoration-none text-danger">
                            {{ $trx->number }}
                        </a>
                    </td>
                    <td style="font-size:0.82rem;">
                        <div>{{ $trx->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted">{{ $trx->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>{{ $trx->user->name }}</td>
                    <td>
                        <span class="badge" style="background:#F5F5F5;color:#424242;">
                            {{ $trx->items->count() }} item
                        </span>
                    </td>
                    <td class="text-end fw-bold">Rp{{ number_format($trx->total,0,',','.') }}</td>
                    <td class="text-center">
                        @if($trx->payment_method === 'cash')
                        <span class="badge" style="background:#E8F5E9;color:#2E7D32;">💵 Cash</span>
                        @else
                        <span class="badge" style="background:#F3E5F5;color:#7B1FA2;">📱 QRIS</span>
                        @endif
                    </td>
                    <td class="text-center">{!! $trx->status_badge !!}</td>
                    <td class="text-center">
                        <a href="{{ route('transactions.show', $trx) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada transaksi ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection