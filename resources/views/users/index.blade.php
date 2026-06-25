@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-700 mb-0">Daftar Pengguna</h5>
    <a href="{{ route('users.create') }}" class="btn btn-pos-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="pos-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-700"
                                 style="width:36px;height:36px;background:{{ $user->isOwner() ? '#FFEBEE' : '#E3F2FD' }};color:{{ $user->isOwner() ? '#C62828' : '#1565C0' }};font-size:0.85rem;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-600" style="font-size:0.88rem;">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <span style="font-size:0.7rem;color:#757575;">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><code style="font-size:0.82rem;">{{ $user->username }}</code></td>
                    <td style="font-size:0.82rem;color:#757575;">{{ $user->email ?? '—' }}</td>
                    <td class="text-center">
                        @if($user->isOwner())
                        <span class="badge" style="background:#FFEBEE;color:#C62828;">👑 Owner</span>
                        @else
                        <span class="badge" style="background:#E3F2FD;color:#1565C0;">🧾 Kasir</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->is_active)
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-top">{{ $users->links() }}</div>
    @endif
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-body text-center p-4">
                <div style="font-size:3rem;margin-bottom:12px;">🗑️</div>
                <h5 class="fw-700 mb-2">Hapus Pengguna?</h5>
                <p class="text-muted mb-4" style="font-size:0.85rem;">
                    Pengguna "<strong id="deleteUserName"></strong>" akan dihapus.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUserForm" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDeleteUser(id, name) {
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteUserForm').action = `/users/${id}`;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>
@endpush
