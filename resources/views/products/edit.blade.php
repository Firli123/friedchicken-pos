@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-700 mb-0">Edit Produk</h5>
                <small class="text-muted">{{ $product->code }} — {{ $product->name }}</small>
            </div>
        </div>

        <div class="pos-card p-4">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-600">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-600">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-600">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-600" style="background:#F5F5F5;">Rp</span>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}"
                                   class="form-control @error('price') is-invalid @enderror" min="0" required>
                        </div>
                        @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-600">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}"
                               class="form-control" min="0">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-600">Foto Produk</label>
                        @if($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$product->image) }}" style="max-height:100px;border-radius:10px;border:2px solid #E0E0E0;">
                            <div><small class="text-muted">Foto saat ini. Upload baru untuk mengganti.</small></div>
                        </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               onchange="previewImage(this)">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="imagePreview" style="display:none;margin-top:10px;">
                            <img id="previewImg" src="" style="max-height:120px;border-radius:10px;border:2px solid #E0E0E0;">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-600" for="isActive">Produk Aktif</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-pos-primary px-5">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
