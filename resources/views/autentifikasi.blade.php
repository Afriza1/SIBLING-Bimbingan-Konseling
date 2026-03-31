@extends('layouts.dashboard')
@section('content')
<div>
  <div class="content">
    <div class="row pt-4">
      <div class="mb-4">

        {{-- NOTIFIKASI USER PENDING --}}
        @if($pendingUsers->count() > 0)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4" style="border-radius:12px;">
          <div style="font-size:24px;">⏳</div>
          <div>
            <strong>{{ $pendingUsers->count() }} pengguna baru</strong> menunggu verifikasi.
            Silakan assign role yang sesuai di bawah.
          </div>
        </div>
        @endif

        {{-- TAB --}}
        <div style="display:flex;gap:0;border-bottom:1px solid #e2e8f0;margin-bottom:16px;">
          <div id="tab-pending-btn" onclick="switchAuthTab('pending')"
            style="padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;
            color:{{ $pendingUsers->count() > 0 ? '#ca8a04' : '#64748b' }};
            border-bottom:2px solid {{ $pendingUsers->count() > 0 ? '#ca8a04' : 'transparent' }};">
            Menunggu Verifikasi
            @if($pendingUsers->count() > 0)
              <span class="badge bg-warning text-dark ms-1">{{ $pendingUsers->count() }}</span>
            @endif
          </div>
          <div id="tab-all-btn" onclick="switchAuthTab('all')"
            style="padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;
            color:{{ $pendingUsers->count() == 0 ? '#185FA5' : '#64748b' }};
            border-bottom:2px solid {{ $pendingUsers->count() == 0 ? '#185FA5' : 'transparent' }};">
            Semua Pengguna
          </div>
        </div>

        {{-- PANEL: MENUNGGU VERIFIKASI --}}
        <div id="panel-pending" style="{{ $pendingUsers->count() > 0 ? '' : 'display:none;' }}">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h5 class="m-0 text-warning">⏳ Pengguna Menunggu Verifikasi</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover" style="width:100%">
                  <thead class="table-light">
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>NIS / NIP</th>
                      <th>Tanggal Daftar</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pendingUsers as $i => $user)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->nomor_induk ?? '-' }}</td>
                      <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y, H:i') }}</td>
                      <td>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $user->id }}">
                          ✓ Verifikasi
                        </button>
                        @can('Hapus Autentifikasi')
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $user->id }}">
                          Tolak
                        </button>
                        @endcan
                      </td>
                    </tr>

                    {{-- Approve Modal --}}
                    <div class="modal fade" id="approveModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog"><div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Verifikasi: {{ $user->name }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('autentifikasi.update', $user->id) }}" method="POST">
                          @csrf @method('PUT')
                          <div class="modal-body">
                            <div class="alert alert-info" style="font-size:13px;">
                              NIS/NIP: <strong>{{ $user->nomor_induk }}</strong><br>
                              Tanggal daftar: <strong>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</strong>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">NIP / NISN</label>
                              <input type="text" class="form-control" name="nomor_induk" value="{{ $user->nomor_induk }}">
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                              <select class="form-select" name="role" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                  <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Verifikasi & Simpan</button>
                          </div>
                        </form>
                      </div></div>
                    </div>

                    {{-- Delete Modal --}}
                    <div class="modal fade" id="deleteRoleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog"><div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Tolak Pendaftaran</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('autentifikasi.destroy', $user->id) }}" method="POST">
                          @csrf @method('DELETE')
                          <div class="modal-body">Yakin ingin menolak dan menghapus pendaftaran <strong>{{ $user->name }}</strong>?</div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak & Hapus</button>
                          </div>
                        </form>
                      </div></div>
                    </div>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada pengguna yang menunggu verifikasi</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- PANEL: SEMUA PENGGUNA --}}
        <div id="panel-all" style="{{ $pendingUsers->count() == 0 ? '' : 'display:none;' }}">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h5 class="m-0 text-primary">Daftar Pengguna</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="example" class="table table-striped" style="width:100%">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Pengguna</th>
                      <th>NIP / NISN</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->nomor_induk ?? '-' }}</td>
                      <td>
                        @if($user->roles->isNotEmpty())
                          @foreach($user->roles as $role)
                            <span class="badge bg-info">{{ $role->name }}</span>
                          @endforeach
                        @else
                          <span class="badge bg-secondary">Tidak ada role</span>
                        @endif
                      </td>
                      <td>
                        @if($user->account_status === 'pending')
                          <span class="badge bg-warning text-dark">Pending</span>
                        @else
                          <span class="badge bg-success">Aktif</span>
                        @endif
                      </td>
                      <td>
                        @can('Ubah Autentifikasi')
                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $user->id }}">Edit Role</a>
                        @endcan
                        @can('Hapus Autentifikasi')
                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">Hapus</a>
                        @endcan

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editRoleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog"><div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Role: {{ $user->name }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('autentifikasi.update', $user->id) }}" method="POST">
                              @csrf @method('PUT')
                              <div class="modal-body">
                                <div class="mb-3">
                                  <label class="form-label">NIP / NISN</label>
                                  <input type="text" class="form-control" name="nomor_induk" value="{{ $user->nomor_induk }}">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Pilih Role</label>
                                  <select class="form-select" name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                      <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                      </option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                              </div>
                            </form>
                          </div></div>
                        </div>

                        {{-- Delete Modal --}}
                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog"><div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Hapus: {{ $user->name }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('autentifikasi.destroy', $user->id) }}" method="POST">
                              @csrf @method('DELETE')
                              <div class="modal-body">Yakin ingin menghapus <strong>{{ $user->name }}</strong>?</div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                              </div>
                            </form>
                          </div></div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function switchAuthTab(tab) {
  document.getElementById('tab-pending-btn').style.color    = tab==='pending' ? '#ca8a04' : '#64748b';
  document.getElementById('tab-pending-btn').style.borderBottom = tab==='pending' ? '2px solid #ca8a04' : '2px solid transparent';
  document.getElementById('tab-all-btn').style.color        = tab==='all' ? '#185FA5' : '#64748b';
  document.getElementById('tab-all-btn').style.borderBottom = tab==='all' ? '2px solid #185FA5' : '2px solid transparent';
  document.getElementById('panel-pending').style.display    = tab==='pending' ? 'block' : 'none';
  document.getElementById('panel-all').style.display        = tab==='all' ? 'block' : 'none';
}
</script>
@endpush
@endsection
