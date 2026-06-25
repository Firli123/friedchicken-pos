@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-700 mb-0">Laporan Bulanan</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.pdf', ['type'=>'monthly', 'month'=>$month->format('Y-m')]) }}"
           class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.export.excel', ['type'=>'monthly', 'month'=>$month->format('Y-m')]) }}"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

{{-- Month Picker --}}
<div class="pos-card p-3 mb-4">
    <form method="GET" class="d-flex align-items-center gap-3">
        <label class="fw-600" style="font-size:0.85rem;">Pilih Bulan:</label>
        <input type="month" name="month" value="{{ $month->format('Y-m') }}"
               class="form-control" style="max-width:200px;" onchange="this.form.submit()">
        <span style="font-size:0.85rem;color:#757575;">
            {{ $month->translatedFormat('F Y') }}
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
            <div class="stat-label mb-2">Cash</div>
            <div class="stat-value" style="color:#2E7D32;font-size:1.2rem;">Rp{{ number_format($totalCash, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">QRIS</div>
            <div class="stat-value" style="color:#7B1FA2;font-size:1.2rem;">Rp{{ number_format($totalQris, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="pos-card p-4 mb-4">
    <h6 class="fw-700 mb-3">Grafik Penjualan Harian — {{ $month->translatedFormat('F Y') }}</h6>
    <canvas id="dailyChart" height="80"></canvas>
</div>

<div class="row g-3">
    {{-- Top Products --}}
    <div class="col-md-5">
        <div class="pos-card p-4">
            <h6 class="fw-700 mb-3">Produk Terlaris Bulan Ini</h6>
            @if($topProducts->isEmpty())
            <div class="text-center text-muted py-4">Tidak ada data</div>
            @else
            <table class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Omzet</th></tr></thead>
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

    {{-- Payment method breakdown --}}
    <div class="col-md-7">
        <div class="pos-card p-4">
            <h6 class="fw-700 mb-3">Perbandingan Metode Pembayaran</h6>
            <canvas id="paymentChart" height="180"></canvas>
            @php
                $totalPay = $totalCash + $totalQris;
                $cashPct  = $totalPay > 0 ? round($totalCash / $totalPay * 100) : 0;
                $qrisPct  = $totalPay > 0 ? round($totalQris / $totalPay * 100) : 0;
            @endphp
            <div class="row mt-3 text-center">
                <div class="col-6">
                    <div style="font-size:1.4rem;font-weight:800;color:#E53935;">{{ $cashPct }}%</div>
                    <div style="font-size:0.78rem;color:#757575;">Cash</div>
                    <div style="font-size:0.85rem;font-weight:600;">Rp{{ number_format($totalCash, 0, ',', '.') }}</div>
                </div>
                <div class="col-6">
                    <div style="font-size:1.4rem;font-weight:800;color:#7B1FA2;">{{ $qrisPct }}%</div>
                    <div style="font-size:0.78rem;color:#757575;">QRIS</div>
                    <div style="font-size:0.85rem;font-weight:600;">Rp{{ number_format($totalQris, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dailyLabels = {!! json_encode($dailyData->pluck('label')) !!};
const dailyValues = {!! json_encode($dailyData->pluck('revenue')) !!};

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: dailyValues,
            backgroundColor: 'rgba(229,57,53,0.2)',
            borderColor: '#E53935',
            borderWidth: 2,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'Rp' + (v/1000).toFixed(0) + 'K' } }
        }
    }
});

new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Cash', 'QRIS'],
        datasets: [{
            data: [{{ $totalCash }}, {{ $totalQris }}],
            backgroundColor: ['#E53935', '#7B1FA2'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
