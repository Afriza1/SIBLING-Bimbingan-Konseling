@extends('layouts.dashboard')
@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h5 class="m-0 text-primary">Tabel Data Karir</h5>
              @can('Tambah Loker')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah</button>
              @endcan
              <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog"><div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('jobVacancy.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                      <div class="mb-3"><label class="col-form-label">Unggah Brosur</label><input type="file" class="form-control" name="pamphlet" accept=".pdf,.jpg,.png"></div>
                      <div class="mb-3"><label class="col-form-label">Nama Perusahaan</label><input type="text" class="form-control" name="company_name" required></div>
                      <div class="mb-3"><label class="col-form-label">Posisi</label><input type="text" class="form-control" name="position" required></div>
                      <div class="mb-3"><label class="col-form-label">Deskripsi</label><textarea class="form-control" name="description" required></textarea></div>
                      <div class="mb-3"><label class="col-form-label">Lokasi</label><input type="text" class="form-control" name="location" required></div>
                      <div class="mb-3"><label class="col-form-label">Gaji</label><input type="text" class="form-control" name="salary" required></div>
                      <div class="mb-3"><label class="col-form-label">Batas Waktu</label><input type="date" class="form-control" name="dateline_date" required></div>
                      <div class="mb-3"><label class="col-form-label">Link</label><input type="text" class="form-control" name="link"></div>
                      <div class="mb-3">
                        <label class="col-form-label">Guru BK</label>
                        <select class="form-control" name="user_id" required>
                          <option value="">-- Pilih Guru BK --</option>
                          @foreach ($users as $user)
                            @if($user->hasRole('Guru BK'))<option value="{{ $user->id }}">{{ $user->name }}</option>@endif
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                  </form>
                </div></div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="card border-0 shadowNavbar" id="panel">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="example" class="table table-hover" style="width:100%;--bs-table-bg:white;">
                        <thead class="text-nowrap table-light" style="--bs-table-bg:#eef2f7;--bs-table-border-color:#eef2f7;">
                          <tr>
                            <th>No</th><th>Brosur</th><th>Nama Perusahaan</th><th>Posisi</th>
                            <th>Deskripsi</th><th>Lokasi</th><th>Gaji</th><th>Batas Waktu</th>
                            <th>Link Pendaftaran</th><th>Ditambah oleh</th><th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($job_vacancies as $job_vacancy)
                          <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                              @if($job_vacancy->pamphlet)
                                @php $ext = strtolower(pathinfo($job_vacancy->pamphlet, PATHINFO_EXTENSION)); @endphp
                                @if($ext === 'pdf')
                                  <i class="uil uil-file-pdf-alt" style="font-size:50px;color:red;"></i>
                                @else
                                  <img src="{{ asset('storage/job_vacancies/' . $job_vacancy->pamphlet) }}"
                                    alt="Brosur" style="max-width:100px;max-height:100px;object-fit:cover;cursor:zoom-in;"
                                    onclick="showFullscreen('{{ asset('storage/job_vacancies/' . $job_vacancy->pamphlet) }}')"
                                    title="Klik untuk lihat penuh">
                                @endif
                              @else
                                <span class="text-muted">Tidak ada</span>
                              @endif
                            </td>
                            <td>{{ $job_vacancy->company_name }}</td>
                            <td>{{ $job_vacancy->position }}</td>
                            <td>{{ $job_vacancy->description }}</td>
                            <td>{{ $job_vacancy->location }}</td>
                            <td>{{ $job_vacancy->salary }}</td>
                            <td>{{ $job_vacancy->dateline_date }}</td>
                            <td>{{ $job_vacancy->link ?? '-' }}</td>
                            <td>{{ optional($job_vacancy->user)->name ?? '-' }}</td>
                            <td>
                              @can('Ubah Loker')
                              <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit_data{{ $job_vacancy->id }}">Edit</a>
                              @endcan
                              @can('Hapus Loker')
                              <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_data{{ $job_vacancy->id }}">Hapus</a>
                              @endcan

                              {{-- Edit Modal --}}
                              <div class="modal fade" id="edit_data{{ $job_vacancy->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog"><div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Edit: {{ $job_vacancy->position }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <form action="{{ route('jobVacancy.update', $job_vacancy->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                      <div class="mb-3">
                                        <label class="col-form-label">Unggah Brosur</label>
                                        @if($job_vacancy->pamphlet)
                                          @php $ext = strtolower(pathinfo($job_vacancy->pamphlet, PATHINFO_EXTENSION)); @endphp
                                          <div class="mb-2">
                                            @if($ext === 'pdf')
                                              <i class="uil uil-file-pdf-alt" style="font-size:50px;color:red;"></i>
                                            @else
                                              <img src="{{ asset('storage/job_vacancies/' . $job_vacancy->pamphlet) }}"
                                                style="max-width:100px;max-height:100px;object-fit:cover;">
                                            @endif
                                          </div>
                                          <p class="text-muted small">Biarkan kosong jika tidak ingin mengganti.</p>
                                        @else
                                          <p class="text-danger small">Belum ada brosur.</p>
                                        @endif
                                        <input type="file" class="form-control" name="pamphlet" accept=".pdf,.jpg,.png">
                                      </div>
                                      <div class="mb-3"><label class="col-form-label">Nama Perusahaan</label><input type="text" class="form-control" name="company_name" value="{{ $job_vacancy->company_name }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Posisi</label><input type="text" class="form-control" name="position" value="{{ $job_vacancy->position }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Deskripsi</label><textarea class="form-control" name="description" required>{{ $job_vacancy->description }}</textarea></div>
                                      <div class="mb-3"><label class="col-form-label">Lokasi</label><input type="text" class="form-control" name="location" value="{{ $job_vacancy->location }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Gaji</label><input type="text" class="form-control" name="salary" value="{{ $job_vacancy->salary }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Batas Waktu</label><input type="date" class="form-control" name="dateline_date" value="{{ $job_vacancy->dateline_date }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Link</label><input type="text" class="form-control" name="link" value="{{ $job_vacancy->link }}"></div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Guru BK</label>
                                        <select class="form-control" name="user_id" required>
                                          <option value="">-- Pilih Guru BK --</option>
                                          @foreach($users as $user)
                                            @if($user->hasRole('Guru BK'))
                                              <option value="{{ $user->id }}" {{ $job_vacancy->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endif
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
                              <div class="modal fade" id="delete_data{{ $job_vacancy->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog"><div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Hapus: {{ $job_vacancy->position }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">Yakin ingin menghapus <strong>{{ $job_vacancy->position }}</strong>?</div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('jobVacancy.destroy', $job_vacancy->id) }}" method="POST">
                                      @csrf @method('DELETE')
                                      <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                  </div>
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
    </div>
  </div>
@endsection
