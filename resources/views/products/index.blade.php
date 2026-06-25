@extends('layouts.app')

@section('title', 'Manajemen Produk')
@section('page-title', 'Manajemen Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-700 mb-1">Daftar Produk</h5>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Kelola semua produk menu Fried Chicken</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-pos-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
</div>

{{-- Filter Bar --}}
<div class="pos-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="🔍 Cari nama/kode produk...">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-pos-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

{{-- Products Table --}}
<div class="pos-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="80">Kode</th>
                    <th>Nama Produk</th>
                    <th width="100">Kategori</th>
                    <th width="120" class="text-end">Harga</th>
                    <th width="80" class="text-center">Status</th>
                    <th width="130" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $product)
                <tr>
                    <td class="text-muted" style="font-size:0.8rem;">{{ $products->firstItem() + $i }}</td>
                    <td>
                        <span class="badge" style="background:#F5F5F5;color:#424242;font-size:0.72rem;font-weight:600;">
                            {{ $product->code }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#F5F5F5;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    @if($product->category->slug === 'paket') 🍱
                                    @elseif($product->category->slug === 'tambahan') 🍚
                                    @else 🍗
                                    @endif
                                @endif
                            </div>
                            <div>
                                <div class="fw-600" style="font-size:0.9rem;">{{ $product->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge"
                            style="background:{{ $product->category->slug === 'ayam' ? '#FFEBEE' : ($product->category->slug === 'paket' ? '#E8EAF6' : '#E8F5E9') }};
                                   color:{{ $product->category->slug === 'ayam' ? '#C62828' : ($product->category->slug === 'paket' ? '#283593' : '#1B5E20') }};
                                   font-size:0.72rem;">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td class="text-end fw-700" style="color:#E53935;">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-flex justify-content-center mb-0">
                            <input class="form-check-input toggle-active"
                                   type="checkbox"
                                   data-id="{{ $product->id }}"
                                   {{ $product->is_active ? 'checked' : '' }}
                                   style="cursor:pointer;width:2em;height:1em;">
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('products.edit', $product) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                    class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <div>Tidak ada produk ditemukan.</div>
                            @if(request('search') || request('category'))
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-danger mt-2">Reset Filter</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $products->links() }}
    </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-body text-center p-4">
                <div style="width:64px;height:64px;background:#FFEBEE;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;">
                    🗑️
                </div>
                <h5 class="fw-700 mb-2">Hapus Produk?</h5>
                <p class="text-muted mb-4" style="font-size:0.85rem;">
                    Produk "<strong id="deleteProductName"></strong>" akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteForm').action = `/products/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Toggle active via AJAX
document.querySelectorAll('.toggle-active').forEach(toggle => {
    toggle.addEventListener('change', async function () {
        const id = this.dataset.id;
        try {
            const res = await fetch(`/products/${id}/toggle`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' },
            });
            const data = await res.json();
            // Optionally show a toast
        } catch (e) {
            this.checked = !this.checked; // revert
        }
    });
});
</script>
@endpush
