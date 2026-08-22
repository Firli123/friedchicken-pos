@extends('layouts.app')
@section('title', 'HPP Harian')
@section('page-title', 'HPP Harian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Harga Pokok Penjualan (HPP)</h5>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Catat pengeluaran harian untuk menghitung laba bersih</p>
    </div>
</div>

{{-- Filter Tanggal --}}
<div class="pos-card p-3 mb-4">
    <form method="GET" class="d-flex align-items-center gap-3">
        <label class="fw-bold" style="font-size:0.85rem;white-space:nowrap;">Pilih Tanggal:</label>
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
               class="form-control" style="max-width:200px;" onchange="this.form.submit()">
        <span style="font-size:0.85rem;color:#757575;">
            {{ $date->translatedFormat('l, d F Y') }}
        </span>
    </form>
</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Total Pendapatan</div>
            <div class="stat-value" style="color:#1565C0;font-size:1.3rem;">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Total HPP</div>
            <div class="stat-value text-danger" style="font-size:1.3rem;">
                Rp{{ number_format($totalHpp, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Laba Kotor</div>
            <div class="stat-value" style="color:{{ $labaKotor >= 0 ? '#2E7D32' : '#C62828' }};font-size:1.3rem;">
                Rp{{ number_format($labaKotor, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label mb-2">Margin Laba</div>
            <div class="stat-value" style="color:{{ $marginPersen >= 30 ? '#2E7D32' : ($marginPersen >= 10 ? '#F57F17' : '#C62828') }};font-size:1.3rem;">
                {{ $marginPersen }}%
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Form Tambah HPP --}}
    <div class="col-md-4">
        <div class="pos-card p-4">
            <h6 class="fw-bold mb-3">➕ Tambah HPP</h6>
            <form action="{{ route('hpp.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.8rem;">Tanggal</label>
                    <input type="date" name="tanggal"
                           class="form-control @error('tanggal') is-invalid @enderror"
                           value="{{ old('tanggal', $date->format('Y-m-d')) }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.8rem;">Kategori</label>
                    <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                        <option value="bahan_baku" {{ old('kategori')==='bahan_baku'?'selected':'' }}>🐔 Bahan Baku</option>
                        <option value="operasional" {{ old('kategori')==='operasional'?'selected':'' }}>⚙️ Operasional</option>
                        <option value="lainnya" {{ old('kategori')==='lainnya'?'selected':'' }}>📦 Lainnya</option>
                    </select>
                    @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.8rem;">Keterangan</label>
                    <input type="text" name="keterangan"
                           class="form-control @error('keterangan') is-invalid @enderror"
                           placeholder="Contoh: Beli ayam 5kg"
                           value="{{ old('keterangan') }}" required>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.8rem;">Jumlah (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold" style="background:#F5F5F5;">Rp</span>
                        <input type="number" name="jumlah"
                               class="form-control @error('jumlah') is-invalid @enderror"
                               placeholder="0" min="1"
                               value="{{ old('jumlah') }}" required>
                    </div>
                    @error('jumlah')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.8rem;">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"
                              placeholder="Keterangan tambahan...">{{ old('catatan') }}</textarea>
                </div>

                <button type="submit" class="btn w-100 fw-bold"
                        style="background:#E53935;color:#fff;border-radius:10px;height:44px;">
                    <i class="bi bi-plus-circle me-1"></i> Tambah HPP
                </button>
            </form>
        </div>

        {{-- Ringkasan per Kategori --}}
        @if($byKategori->isNotEmpty())
        <div class="pos-card p-4 mt-3">
            <h6 class="fw-bold mb-3">📊 Per Kategori</h6>
            @foreach($byKategori as $kat => $total)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span style="font-size:0.85rem;">
                    @if($kat === 'bahan_baku') 🐔 Bahan Baku
                    @elseif($kat === 'operasional') ⚙️ Operasional
                    @else 📦 Lainnya
                    @endif
                </span>
                <span class="fw-bold text-danger" style="font-size:0.85rem;">
                    Rp{{ number_format($total, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Daftar HPP --}}
    <div class="col-md-8">
        <div class="pos-card">
            <div class="p-4 border-bottom">
                <h6 class="fw-bold mb-0">Daftar HPP — {{ $date->format('d/m/Y') }}</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Kategori</th>
                            <th class="text-end">Jumlah</th>
                            <th>Dicatat oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hppList as $hpp)
                        <tr>
                            <td>
                                <div class="fw-bold" style="font-size:0.88rem;">{{ $hpp->keterangan }}</div>
                                @if($hpp->catatan)
                                <div style="font-size:0.75rem;color:#9e9e9e;">{{ $hpp->catatan }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="
                                    background:{{ $hpp->kategori === 'bahan_baku' ? '#FFF3E0' : ($hpp->kategori === 'operasional' ? '#E3F2FD' : '#F3E5F5') }};
                                    color:{{ $hpp->kategori === 'bahan_baku' ? '#E65100' : ($hpp->kategori === 'operasional' ? '#1565C0' : '#7B1FA2') }};
                                    font-size:0.72rem;">
                                    {{ $hpp->kategori_label }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-danger">
                                Rp{{ number_format($hpp->jumlah, 0, ',', '.') }}
                            </td>
                            <td style="font-size:0.82rem;color:#757575;">{{ $hpp->user->name }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editHpp({{ $hpp->id }}, '{{ $hpp->tanggal->format('Y-m-d') }}', '{{ addslashes($hpp->keterangan) }}', {{ $hpp->jumlah }}, '{{ $hpp->kategori }}', '{{ addslashes($hpp->catatan ?? '') }}')"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('hpp.destroy', $hpp) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus HPP ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada HPP untuk tanggal ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($hppList->isNotEmpty())
                    <tfoot style="border-top:2px solid #E53935;">
                        <tr>
                            <td colspan="2" class="fw-bold text-end">TOTAL HPP</td>
                            <td class="text-end fw-bold text-danger" style="font-size:1rem;">
                                Rp{{ number_format($totalHpp, 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#E53935;color:#fff;border:none;">
                <h5 class="modal-title fw-bold">Edit HPP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem;">Tanggal</label>
                        <input type="date" name="tanggal" id="editTanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem;">Kategori</label>
                        <select name="kategori" id="editKategori" class="form-select" required>
                            <option value="bahan_baku">🐔 Bahan Baku</option>
                            <option value="operasional">⚙️ Operasional</option>
                            <option value="lainnya">📦 Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem;">Keterangan</label>
                        <input type="text" name="keterangan" id="editKeterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem;">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold" style="background:#F5F5F5;">Rp</span>
                            <input type="number" name="jumlah" id="editJumlah" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem;">Catatan</label>
                        <textarea name="catatan" id="editCatatan" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold"
                            style="background:#E53935;color:#fff;border-radius:10px;height:44px;">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editHpp(id, tanggal, keterangan, jumlah, kategori, catatan) {
    document.getElementById('editForm').action = `/hpp/${id}`;
    document.getElementById('editTanggal').value    = tanggal;
    document.getElementById('editKeterangan').value = keterangan;
    document.getElementById('editJumlah').value     = jumlah;
    document.getElementById('editKategori').value   = kategori;
    document.getElementById('editCatatan').value    = catatan;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endpush