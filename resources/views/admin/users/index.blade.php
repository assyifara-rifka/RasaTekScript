@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-users"></i> Kelola Pengguna</h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Total Pesanan</th>
                            <th>Bergabung</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $user->orders_count }} pesanan</span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteUser({{ $user->id }})" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $user->id }}" 
                                          action="{{ route('admin.users.destroy', $user->id) }}" 
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Belum ada pengguna</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        document.getElementById('delete-form-' + userId).submit();
    }
}
</script>
@endsection
