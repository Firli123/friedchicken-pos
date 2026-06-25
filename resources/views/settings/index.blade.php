@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="pos-card p-4">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Store Info --}}
                <h6 class="fw-700 mb-3" style="color:#E53935;border-bottom:2px solid #E53935;padding-bottom:6px;">
                    🏪 Informasi Toko
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control"
                               value="{{ old('store_name', $settings['store_name'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Tagline</label>
                        <input type="text" name="store_tagline" class="form-control"
                               value="{{ old('store_tagline', $settings['store_tagline'] ?? '') }}"
                               placeholder="Contoh: Ayam Goreng Crispy Enak">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Alamat</label>
                        <input type="text" name="store_address" class="form-control"
                               value="{{ old('store_address', $settings['store_address'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">No. Telepon</label>
                        <input type="text" name="store_phone" class="form-control"
                               value="{{ old('store_phone', $settings['store_phone'] ?? '') }}">
                    </div>
                </div>

                {{-- Receipt --}}
                <h6 class="fw-700 mb-3" style="color:#E53935;border-bottom:2px solid #E53935;padding-bottom:6px;">
                    🧾 Pengaturan Struk
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-600">Ukuran Kertas Thermal</label>
                        <select name="receipt_paper_size" class="form-select">
                            <option value="58" {{ ($settings['receipt_paper_size'] ?? '80') == '58' ? 'selected' : '' }}>58 mm</option>
                            <option value="80" {{ ($settings['receipt_paper_size'] ?? '80') == '80' ? 'selected' : '' }}>80 mm</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-600">Pesan Footer Struk</label>
                        <textarea name="receipt_footer" class="form-control" rows="2"
                                  placeholder="Terima Kasih&#10;Selamat Menikmati">{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Finance --}}
                <h6 class="fw-700 mb-3" style="color:#E53935;border-bottom:2px solid #E53935;padding-bottom:6px;">
                    💰 Keuangan
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-600">Tarif Pajak (%)</label>
                        <div class="input-group">
                            <input type="number" name="tax_rate" class="form-control"
                                   value="{{ old('tax_rate', $settings['tax_rate'] ?? 0) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Isi 0 jika tidak ada pajak</small>
                    </div>
                </div>

                {{-- QRIS --}}
                <h6 class="fw-700 mb-3" style="color:#E53935;border-bottom:2px solid #E53935;padding-bottom:6px;">
                    📱 QRIS Statis
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Upload Gambar QRIS</label>
                        <input type="file" name="qris_image" class="form-control" accept="image/jpg,image/jpeg,image/png"
                               onchange="previewQris(this)">
                        <small class="text-muted">JPG/PNG, maks 1MB. Gambar QR dari bank/e-wallet Anda.</small>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        @if(!empty($settings['qris_image']))
                        <div>
                            <div style="font-size:0.8rem;color:#757575;margin-bottom:6px;">QR saat ini:</div>
                            <img src="{{ asset('storage/'.$settings['qris_image']) }}"
                                 style="max-height:120px;border-radius:8px;border:2px solid #E0E0E0;">
                        </div>
                        @else
                        <div class="text-center text-muted" style="width:120px;height:120px;border:2px dashed #E0E0E0;border-radius:8px;display:flex;align-items:center;justify-content:center;" id="qrisPreviewBox">
                            <div>
                                <i class="bi bi-qr-code fs-2 d-block"></i>
                                <small>Belum ada QR</small>
                            </div>
                        </div>
                        @endif
                        <div id="qrisNewPreview" style="display:none;margin-left:12px;">
                            <div style="font-size:0.8rem;color:#757575;margin-bottom:6px;">Preview baru:</div>
                            <img id="qrisPreviewImg" style="max-height:120px;border-radius:8px;border:2px solid #E53935;">
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-pos-primary px-5">
                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewQris(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('qrisPreviewImg').src = e.target.result;
            document.getElementById('qrisNewPreview').style.display = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
