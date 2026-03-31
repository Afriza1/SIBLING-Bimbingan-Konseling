@extends('layouts.dashboard')
@section('content')

<div class="content">
  <div class="row pt-4">
    <div class="mb-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h5 class="m-0 text-primary">Daftar Asesmen</h5>
          @if(in_array($role, ['Admin', 'Guru BK']))
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Tambah Asesmen</button>
          @endif
        </div>

        {{-- Modal Tambah --}}
        @if(in_array($role, ['Admin', 'Guru BK']))
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Asesmen</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('assessment_link.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Nama Asesmen <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" required placeholder="Contoh: DCM (Daftar Cek Masalah)">
                </div>
                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi singkat asesmen..."></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Icon / Logo <span class="text-muted small">(jpg, png, svg — maks 2MB)</span></label>
                  <input type="file" class="form-control" name="icon" accept=".jpg,.jpeg,.png,.svg">
                </div>
                <div class="mb-3">
                  <label class="form-label">Link URL <span class="text-danger">*</span></label>
                  <input type="url" class="form-control" name="url" required placeholder="https://...">
                  <small class="text-muted">Link website asesmen resmi yang akan dibuka siswa</small>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div></div>
        </div>
        @endif

        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          {{-- GRID KARTU ASESMEN --}}
          @if($links->isEmpty())
            <div class="text-center py-5 text-muted">
              <i class="uil uil-clipboard-notes" style="font-size:48px;opacity:0.3;"></i>
              <p class="mt-2">Belum ada asesmen yang ditambahkan.</p>
            </div>
          @else
          <div class="row g-3">
            @foreach($links as $link)
            <div class="col-xl-3 col-md-4 col-sm-6">
              <div class="card h-100 border" style="border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;"
                onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
                onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">
                  {{-- Icon --}}
                  <div style="width:72px;height:72px;border-radius:16px;background:#f0f4f8;display:flex;align-items:center;justify-content:center;margin-bottom:16px;overflow:hidden;">
                    @if($link->icon)
                      <img src="{{ asset('storage/' . $link->icon) }}" style="width:100%;height:100%;object-fit:contain;">
                    @else
                      <i class="uil uil-clipboard-notes" style="font-size:36px;color:#2563eb;"></i>
                    @endif
                  </div>

                  {{-- Info --}}
                  <h6 class="fw-bold mb-1" style="font-size:14px;">{{ $link->name }}</h6>
                  @if($link->description)
                    <p class="text-muted mb-3" style="font-size:12px;line-height:1.5;">{{ Str::limit($link->description, 80) }}</p>
                  @else
                    <p class="text-muted mb-3" style="font-size:12px;">-</p>
                  @endif

                  {{-- Tombol Buka --}}
                  <a href="{{ $link->url }}" target="_blank" class="btn btn-primary btn-sm w-100 mt-auto">
                    <i class="uil uil-external-link-alt me-1"></i> Buka Asesmen
                  </a>

                  {{-- Aksi admin/guru bk --}}
                  @if(in_array($role, ['Admin', 'Guru BK']))
                  <div class="d-flex gap-2 mt-2 w-100">
                    <button class="btn btn-warning btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editModal{{ $link->id }}">Edit</button>
                    <button class="btn btn-danger btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $link->id }}">Hapus</button>
                  </div>
                  @endif
                </div>
                <div class="card-footer text-muted" style="font-size:11px;border-top:1px solid #f0f0f0;">
                  Ditambah oleh {{ optional($link->user)->name ?? '-' }}
                </div>
              </div>
            </div>

            {{-- Edit Modal --}}
            @if(in_array($role, ['Admin', 'Guru BK']))
            <div class="modal fade" id="editModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit: {{ $link->name }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('assessment_link.update', $link->id) }}" method="POST" enctype="multipart/form-data">
                  @csrf @method('PUT')
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Nama Asesmen</label>
                      <input type="text" class="form-control" name="name" value="{{ $link->name }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Deskripsi</label>
                      <textarea class="form-control" name="description" rows="3">{{ $link->description }}</textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Icon / Logo</label>
                      @if($link->icon)
                        <div class="mb-2">
                          <img src="{{ asset('storage/' . $link->icon) }}" style="height:48px;border-radius:8px;">
                          <small class="text-muted ms-2">Biarkan kosong jika tidak ingin mengganti.</small>
                        </div>
                      @endif
                      <input type="file" class="form-control" name="icon" accept=".jpg,.jpeg,.png,.svg">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Link URL</label>
                      <input type="url" class="form-control" name="url" value="{{ $link->url }}" required>
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
            <div class="modal fade" id="deleteModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Hapus Asesmen</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Yakin ingin menghapus <strong>{{ $link->name }}</strong>?</div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  <form action="{{ route('assessment_link.destroy', $link->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                  </form>
                </div>
              </div></div>
            </div>
            @endif

            @endforeach
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
