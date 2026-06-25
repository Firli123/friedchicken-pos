@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h5 class="fw-700 mb-0">Edit Pengguna</h5>
                <small class="text-muted">{{ $user->name }} ({{ $user->username }})</small>
            </div>
        </div>
        <div class="pos-card p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" required>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Role</label>
                        <select name="role" class="form-select" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                            <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Owner</option>
                        </select>
                        @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        @endif
                    </div>
                    <div class="col-12">
                        <div class="alert" style="background:#FFF8E1;border:1px solid #FFD54F;border-radius:8px;font-size:0.82rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Kosongkan field password jika tidak ingin mengubah password.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }} style="width:2.5em;height:1.3em;"
                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <label class="form-check-label fw-600" for="isActive">Pengguna Aktif</label>
                        </div>
                        @if($user->id === auth()->id())
                        <input type="hidden" name="is_active" value="1">
                        @endif
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-pos-primary px-5"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
