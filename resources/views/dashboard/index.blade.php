@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-2">Pendapatan Hari Ini</div>
                    <div class="stat-value text-danger">Rp{{ number_format($todayRevenue,0,',','.') }}</div>
                </div>
                <div class="stat-icon" style="background:#FFEBEE;color:#E53935;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-2">Pendapatan Bulan Ini</div>
                    <div class="stat-value" style="color:#1565C0;">Rp{{ number_format($monthRevenue,0,',','.') }}</div>
                </div>
                <div class="stat-icon" style="background:#E3F2FD;color:#1565C0;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-2">Transaksi Hari Ini</div>
                    <div class="stat-value" style="color:#2E7D32;">{{ $todayCount }}</div>
                    <small class="text-muted">transaksi</small>
                </div>
                <div class="stat-icon" style="background:#E8F5E9;color:#2E7D32;">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-2">Cash / QRIS Hari Ini</div>
                    <div class="mt-1">
                        <span class="badge" style="background:#E8F5E9;color:#2E7D32;">Cash: Rp{{ number_format($todayCash,0,',','.') }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="badge" style="background:#F3E5F5;color:#7B1FA2;">QRIS: Rp{{ number_format($todayQris,0,',','.') }}</span>
                    </div>
                </div>
                <div class="stat-icon" style="background:#F3E5F5;color:#7B1FA2;">
                    <i class="bi bi-qr-code"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="pos-card p-4">
            <h6 class="fw-bold mb-3">Penjualan 7 Hari Terakhir</h6>
            <canvas id="dailyChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="pos-card p-4">
            <h6 class="fw-bold mb-3">Metode Pembayaran Hari Ini</h6>
            <canvas id="payChart" height="200"></canvas>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <span style="font-size:0.78rem;"><span class="badge" style="background:#E53935;">&nbsp;</span> Cash</span>
                <span style="font-size:0.78rem;"><span class="badge" style="background:#7B1FA2;">&nbsp;</span> QRIS</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-6">
        <div class="pos-card p-4">
            <h6 class="fw-bold mb-3">Penjualan 6 Bulan Terakhir</h6>
            <canvas id="monthChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="pos-card p-4">
            <h6 class="fw-bold mb-3">Produk Terlaris Hari Ini</h6>
            @if($topProducts->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada transaksi hari ini
            </div>
            @else
            <table class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Produk</th><th class="text-end">Terjual</th></tr></thead>
                <tbody>
                    @foreach($topProducts as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td class="fw-bold" style="font-size:0.85rem;">{{ $p->product_name }}</td>
                        <td class="text-end fw-bold text-danger">{{ $p->total_qty }} pcs</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="pos-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Transaksi Terbaru</h6>
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
                <tr>
                    <td>
                        <a href="{{ route('transactions.show',$trx) }}"
                           class="fw-bold text-decoration-none text-danger">{{ $trx->number }}</a>
                    </td>
                    <td style="font-size:0.82rem;color:#757575;">{{ $trx->created_at->format('H:i') }}</td>
                    <td>{{ $trx->user->name }}</td>
                    <td class="fw-bold">Rp{{ number_format($trx->total,0,',','.') }}</td>
                    <td>
                        @if($trx->payment_method==='cash')
                        <span class="badge" style="background:#E8F5E9;color:#2E7D32;">Cash</span>
                        @else
                        <span class="badge" style="background:#F3E5F5;color:#7B1FA2;">QRIS</span>
                        @endif
                    </td>
                    <td>{!! $trx->status_badge !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi hari ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const dailyLabels  = {!! json_encode($dailyChart->pluck('label')) !!};
const dailyData    = {!! json_encode($dailyChart->pluck('revenue')) !!};
const monthLabels  = {!! json_encode($monthlyChart->pluck('label')) !!};
const monthData    = {!! json_encode($monthlyChart->pluck('revenue')) !!};

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{ label: 'Pendapatan', data: dailyData, backgroundColor: 'rgba(229,57,53,0.15)', borderColor: '#E53935', borderWidth: 2, borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp'+(v/1000).toFixed(0)+'K' } } } }
});

new Chart(document.getElementById('payChart'), {
    type: 'doughnut',
    data: {
        labels: ['Cash','QRIS'],
        datasets: [{ data: [{{ $todayCash }}, {{ $todayQris }}], backgroundColor: ['#E53935','#7B1FA2'], borderWidth: 0 }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('monthChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{ label: 'Pendapatan', data: monthData, fill: true, backgroundColor: 'rgba(229,57,53,0.08)', borderColor: '#E53935', borderWidth: 2, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#E53935' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp'+(v/1000).toFixed(0)+'K' } } } }
});
</script>
@endpush