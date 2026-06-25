@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('page-title', 'Backup & Restore Database')

@section('content')
<div class="row g-4">

    {{-- Backup --}}
    <div class="col-md-6">
        <div class="pos-card p-4 h-100">
            <h6 class="fw-700 mb-1" style="color:#2E7D32;">
                <i class="bi bi-cloud-arrow-up me-2"></i>Backup Database
            </h6>
            <p class="text-muted mb-4" style="font-size:0.82rem;">
                Buat salinan database SQLite ke folder backup lokal. Lakukan backup rutin setiap hari.
            </p>

            <form action="{{ route('backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn w-100 fw-700" style="background:#2E7D32;color:#fff;border-radius:10px;padding:12px;"
                        onclick="return confirm('Buat backup database sekarang?')">
                    <i class="bi bi-download me-2"></i> Buat Backup Sekarang
                </button>
            </form>

            <div class="mt-4">
                <h6 class="fw-700 mb-3" style="font-size:0.85rem;">Daftar Backup ({{ $backups->count() }})</h6>
                @if($backups->isEmpty())
                <div class="text-center text-muted py-3">
                    <i class="bi bi-archive fs-2 d-block mb-2"></i>
                    Belum ada backup
                </div>
                @else
                <div style="max-height:320px;overflow-y:auto;">
                    @foreach($backups as $backup)
                    <div class="d-flex align-items-center gap-2 p-2 mb-1" style="background:#F9F9F9;border-radius:8px;border:1px solid #F0F0F0;">
                        <i class="bi bi-database text-success"></i>
                        <div class="flex-1" style="flex:1;min-width:0;">
                            <div style="font-size:0.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $backup['name'] }}
                            </div>
                            <div style="font-size:0.72rem;color:#757575;">
                                {{ $backup['date']->format('d/m/Y H:i') }} &middot;
                                {{ number_format($backup['size'] / 1024, 1) }} KB
                            </div>
                        </div>
                        <a href="{{ route('backup.download', $backup['name']) }}"
                           class="btn btn-sm btn-outline-primary" title="Download" style="font-size:0.75rem;">
                            <i class="bi bi-download"></i>
                        </a>
                        <form action="{{ route('backup.delete', $backup['name']) }}" method="POST"
                              onsubmit="return confirm('Hapus backup ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" style="font-size:0.75rem;">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Restore --}}
    <div class="col-md-6">
        <div class="pos-card p-4 h-100">
            <h6 class="fw-700 mb-1" style="color:#E53935;">
                <i class="bi bi-cloud-arrow-down me-2"></i>Restore Database
            </h6>
            <p class="text-muted mb-4" style="font-size:0.82rem;">
                Pulihkan database dari file backup yang sebelumnya disimpan. Semua data saat ini akan digantikan.
            </p>

            <div class="alert" style="background:#FFF8E1;border:1px solid #FFD54F;border-radius:10px;font-size:0.82rem;color:#5D4037;">
                <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                <strong>Perhatian!</strong> Restore akan menimpa seluruh data saat ini. Pastikan Anda telah melakukan backup terbaru sebelum restore.
            </div>

            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                  onsubmit="return confirm('PERHATIAN!\n\nSeluruh data saat ini akan digantikan dengan data dari file backup.\n\nYakin ingin melanjutkan restore?')">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-600">Upload File Backup (.sqlite)</label>
                    <input type="file" name="backup_file" class="form-control @error('backup_file') is-invalid @enderror"
                           accept=".sqlite" required>
                    @error('backup_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Hanya file .sqlite yang valid</small>
                </div>

                <button type="submit" class="btn w-100 fw-700" style="background:#E53935;color:#fff;border-radius:10px;padding:12px;">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Restore Database
                </button>
            </form>

            <div class="mt-4 p-3" style="background:#F5F5F5;border-radius:10px;">
                <h6 class="fw-700 mb-2" style="font-size:0.82rem;">📋 Panduan Backup Rutin</h6>
                <ul style="font-size:0.78rem;color:#757575;margin:0;padding-left:16px;">
                    <li>Lakukan backup setiap akhir hari operasional</li>
                    <li>Simpan file backup di tempat aman (USB/cloud)</li>
                    <li>Jangan hapus backup lebih dari 7 hari terakhir</li>
                    <li>Test restore secara berkala untuk memastikan backup valid</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
