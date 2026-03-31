@extends('layouts.dashboard')
@section('content')
<div>
    <div class="content">
        <div class="row pt-4">
            <div class="mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-primary">Tabel Role dan Permission</h5>
                        @can('Tambah Perizinan')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            Tambah Perizinan
                        </button>
                        @endcan
                    </div>
                    <div class="dt-container">
                        <div class="row mt-2 justify-content-between">
                            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto"></div>
                            <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Role</th>
                                        <th>Permissions</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <ul>
                                                    @foreach ($role->permissions as $permission)
                                                        <li>{{ $permission->name }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                @can('Ubah Perizinan')
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editPermissionsModal{{ $role->id }}">
                                                    Edit Permissions
                                                </button>
                                                @endcan

                                                <!-- Edit Permissions Modal -->
                                                <div class="modal fade" id="editPermissionsModal{{ $role->id }}" tabindex="-1"
                                                    aria-labelledby="editPermissionsModalLabel{{ $role->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editPermissionsModalLabel{{ $role->id }}">
                                                                    Edit Permissions untuk {{ $role->name }}</h5>
                                                                <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('permission.update', $role->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                   name="permissions[]"
                                                                                   value="{{ $permission->id }}"
                                                                                   {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                                                            <label class="form-check-label">{{ $permission->name }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Tutup</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Role</th>
                                        <th>Permissions</th>
                                        <th>Aksi</th>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
