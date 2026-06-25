@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0">{{ $transaction->number }}</h5>
                    <small class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</small>
                </div>
            </div>
            <button onclick="printReceipt()" class="btn btn-outline-primary">
                <i class="bi bi-printer me-1"></i> Cetak Ulang
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="pos-card p-4 h-100">
                    <h6 class="fw-bold mb-3" style="font-size:0.85rem;text-transform:uppercase;color:#757575;">Info Transaksi</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.85rem;">
                        <tr>
                            <td class="text-muted ps-0">Nomor</td>
                            <td class="fw-bold pe-0 text-end">{{ $transaction->number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Tanggal</td>
                            <td class="fw-bold pe-0 text-end">{{ $transaction->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Jam</td>
                            <td class="fw-bold pe-0 text-end">{{ $transaction->created_at->format('H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Kasir</td>
                            <td class="fw-bold pe-0 text-end">{{ $transaction->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Metode</td>
                            <td class="pe-0 text-end">
                                @if($transaction->payment_method === 'cash')
                                <span class="badge" style="background:#E8F5E9;color:#2E7D32;">Cash</span>
                                @else
                                <span class="badge" style="background:#F3E5F5;color:#7B1FA2;">QRIS</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status</td>
                            <td class="pe-0 text-end">{!! $transaction->status_badge !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-8">
                <div class="pos-card p-4">
                    <h6 class="fw-bold mb-3" style="font-size:0.85rem;text-transform:uppercase;color:#757575;">Item Pesanan</h6>
                    <table class="table mb-0" style="font-size:0.88rem;">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            <tr>
                                <td class="fw-bold">{{ $item->product_name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end text-muted">Rp{{ number_format($item->product_price,0,',','.') }}</td>
                                <td class="text-end fw-bold">Rp{{ number_format($item->subtotal,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="border-top:2px solid #E0E0E0;">
                            <tr>
                                <td colspan="3" class="text-end text-muted">Subtotal</td>
                                <td class="text-end fw-bold">Rp{{ number_format($transaction->subtotal,0,',','.') }}</td>
                            </tr>
                            @if($transaction->discount > 0)
                            <tr>
                                <td colspan="3" class="text-end text-muted">Diskon</td>
                                <td class="text-end text-success fw-bold">- Rp{{ number_format($transaction->discount,0,',','.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-bold" style="font-size:1rem;">TOTAL</td>
                                <td class="text-end fw-bold text-danger" style="font-size:1.1rem;">Rp{{ number_format($transaction->total,0,',','.') }}</td>
                            </tr>
                            @if($transaction->payment_method === 'cash')
                            <tr>
                                <td colspan="3" class="text-end text-muted">Bayar</td>
                                <td class="text-end fw-bold">Rp{{ number_format($transaction->amount_paid,0,',','.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Kembalian</td>
                                <td class="text-end fw-bold text-success">Rp{{ number_format($transaction->change_amount,0,',','.') }}</td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="receiptForPrint" style="display:none;">
<pre style="font-family:'Courier New',monospace;font-size:11px;">
================================================
{{ \App\Models\Setting::get('store_name','FRIED CHICKEN') }}
================================================
{{ $transaction->number }}
{{ $transaction->created_at->format('d/m/Y H:i') }}
Kasir: {{ $transaction->user->name }}
--------------------------------
@foreach($transaction->items as $item){{ $item->product_name }} x{{ $item->quantity }}
                    {{ number_format($item->subtotal,0,'.','.') }}
@endforeach--------------------------------
TOTAL       {{ number_format($transaction->total,0,'.','.') }}
METODE      {{ strtoupper($transaction->payment_method) }}
BAYAR       {{ number_format($transaction->amount_paid,0,'.','.') }}
@if($transaction->change_amount > 0)KEMBALI     {{ number_format($transaction->change_amount,0,'.','.') }}
@endif================================================
{{ \App\Models\Setting::get('receipt_footer',"Terima Kasih\nSelamat Menikmati") }}
================================================</pre>
</div>
@endsection

@push('scripts')
<script>
function printReceipt() {
    const content = document.getElementById('receiptForPrint').innerHTML;
    const win = window.open('','_blank','width=400,height=600');
    win.document.write(`<html><head><title>Struk</title><style>body{margin:0;padding:8px;}pre{font-size:11px;margin:0;}</style></head><body>${content}<script>window.print();<\/script></body></html>`);
    win.document.close();
}
</script>
@endpush