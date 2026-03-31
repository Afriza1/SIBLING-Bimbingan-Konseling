@extends('layouts.dashboard')

@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h5 class="m-0 text-primary">Tabel Data Siswa</h5>
              @can('Tambah Siswa')
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">Tambah Siswa</button>
                <a href="{{ route('student.export') }}" class="btn btn-success">Ekspor ke Excel</a>
              </div>
              @endcan

              {{-- Modal Tambah --}}
              <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog"><div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                      <div class="text-center mb-3">
                        <img id="output" style="width:90px;height:90px;object-fit:cover;border-radius:50%;background:#eee;" src="" alt="">
                      </div>
                      <div class="mb-3"><label class="col-form-label">Unggah Foto</label><input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png" onchange="loadFile(event)"></div>
                      <div class="mb-3"><label class="col-form-label">NISN</label><input type="text" class="form-control" name="nisn" required></div>
                      <div class="mb-3"><label class="col-form-label">Nama</label><input type="text" class="form-control" name="name" required></div>
                      <div class="mb-3">
                        <label class="col-form-label">Kelas</label>
                        <select class="form-control" name="class_id" required>
                          <option value="">-- Pilih Kelas --</option>
                          @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_level }} - {{ $class->major->major_name }} - {{ $class->classroom }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Jenis Kelamin</label>
                        <select class="form-control" name="gender" required>
                          <option value="">-- Pilih --</option>
                          <option value="Laki-laki">Laki-laki</option>
                          <option value="Perempuan">Perempuan</option>
                        </select>
                      </div>
                      <div class="mb-3"><label class="col-form-label">Tempat Lahir</label><input type="text" class="form-control" name="place_of_birth" required></div>
                      <div class="mb-3"><label class="col-form-label">Tanggal Lahir</label><input type="date" class="form-control" name="date_of_birth" required></div>
                      <div class="mb-3">
                        <label class="col-form-label">Agama</label>
                        <select class="form-control" name="religion" required>
                          <option value="">-- Pilih --</option>
                          @foreach(['Islam','Kristen','Katholik','Hindu','Budha','Konghucu'] as $agama)
                            <option value="{{ $agama }}">{{ $agama }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3"><label class="col-form-label">Nomor Telepon</label><input type="text" class="form-control" name="phone_number" required></div>
                      <div class="mb-3"><label class="col-form-label">Alamat</label><textarea class="form-control" name="address" rows="3" required></textarea></div>
                      <div class="mb-3"><label class="col-form-label">Tanggal Masuk</label><input type="date" class="form-control" name="admission_date" required></div>
                      <div class="mb-3"><label class="col-form-label">Nama Wali</label><input type="text" class="form-control" name="guardian_name" required></div>
                      <div class="mb-3"><label class="col-form-label">Nomor Wali</label><input type="text" class="form-control" name="guardian_phone_number" required></div>
                      <div class="mb-3">
                        <label class="col-form-label">Status</label>
                        <select class="form-control" name="status_id" required>
                          <option value="">-- Pilih Status --</option>
                          @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3"><label class="col-form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                  </form>
                </div></div>
              </div>

              {{-- Modal Import --}}
              <div class="modal fade" id="importSiswaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog"><div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('student.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                      <div class="mb-3"><label class="form-label">Pilih File Excel</label><input type="file" name="file" class="form-control" required></div>
                      <a href="{{ route('student.download_format') }}" class="btn btn-sm btn-success">Download Format Excel</a>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary">Import Data</button>
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
                      <table id="example" class="table table-hover student" style="width:100%;--bs-table-bg:white;">
                        <thead class="text-nowrap table-light" style="--bs-table-bg:#eef2f7;--bs-table-border-color:#eef2f7;">
                          <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Agama</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th>Wali</th>
                            <th>No Wali</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Masuk</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($students as $student)
                          <tr style="font-size:14px;">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                              @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:50%;cursor:zoom-in;"
                                    onclick="showFullscreen('{{ asset('storage/' . $student->photo) }}')"
                                    title="Klik untuk lihat foto">
                                @else
                                <span class="text-muted small">Tidak ada foto</span>
                                @endif
                            </td>
                            <td>{{ $student->nisn }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ optional($student->class)->class_level }} {{ optional($student->class->major)->major_name }} {{ optional($student->class)->classroom }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->place_of_birth }}</td>
                            <td>{{ $student->date_of_birth }}</td>
                            <td>{{ $student->religion }}</td>
                            <td>{{ $student->phone_number }}</td>
                            <td>{{ $student->address }}</td>
                            <td>{{ $student->guardian_name }}</td>
                            <td>{{ $student->guardian_phone_number }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ optional($student->status)->status_name }}</td>
                            <td>{{ $student->admission_date }}</td>
                            <td>
                              @can('Ubah Siswa')
                              <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit_data{{ $student->id }}">Edit</a>
                              @endcan
                              @can('Hapus Siswa')
                              <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_data{{ $student->id }}">Hapus</a>
                              @endcan

                              {{-- Edit Modal --}}
                              <div class="modal fade" id="edit_data{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog"><div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Edit: {{ $student->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <form action="{{ route('student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                      <div class="text-center mb-3">
                                        @if($student->photo)
                                          <img id="outputUpdate{{ $student->id }}"
                                            src="{{ asset('storage/' . $student->photo) }}"
                                            style="width:90px;height:90px;object-fit:cover;border-radius:50%;">
                                        @else
                                          <img id="outputUpdate{{ $student->id }}" src=""
                                            style="width:90px;height:90px;object-fit:cover;border-radius:50%;background:#eee;">
                                        @endif
                                      </div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Unggah Foto</label>
                                        @if($student->photo)
                                          <p class="text-muted small mb-1">Biarkan kosong jika tidak ingin mengganti.</p>
                                        @else
                                          <p class="text-danger small mb-1">Belum ada foto.</p>
                                        @endif
                                        <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png"
                                          onchange="loadFileUpdate(event, {{ $student->id }})">
                                      </div>
                                      <div class="mb-3"><label class="col-form-label">NISN</label><input type="text" class="form-control" name="nisn" value="{{ $student->nisn }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Nama</label><input type="text" class="form-control" name="name" value="{{ $student->name }}" required></div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Kelas</label>
                                        <select class="form-control" name="class_id" required>
                                          <option value="">-- Pilih Kelas --</option>
                                          @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ $class->id == $student->class_id ? 'selected' : '' }}>
                                              {{ $class->class_level }} - {{ $class->major->major_name }} - {{ $class->classroom }}
                                            </option>
                                          @endforeach
                                        </select>
                                      </div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Jenis Kelamin</label>
                                        <select class="form-control" name="gender" required>
                                          <option value="">-- Pilih --</option>
                                          <option value="Laki-laki" {{ $student->gender=='Laki-laki'?'selected':'' }}>Laki-laki</option>
                                          <option value="Perempuan" {{ $student->gender=='Perempuan'?'selected':'' }}>Perempuan</option>
                                        </select>
                                      </div>
                                      <div class="mb-3"><label class="col-form-label">Tempat Lahir</label><input type="text" class="form-control" name="place_of_birth" value="{{ $student->place_of_birth }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Tanggal Lahir</label><input type="date" class="form-control" name="date_of_birth" value="{{ $student->date_of_birth }}" required></div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Agama</label>
                                        <select class="form-control" name="religion" required>
                                          <option value="">-- Pilih --</option>
                                          @foreach(['Islam','Kristen','Katholik','Hindu','Budha','Konghucu'] as $agama)
                                            <option value="{{ $agama }}" {{ $student->religion==$agama?'selected':'' }}>{{ $agama }}</option>
                                          @endforeach
                                        </select>
                                      </div>
                                      <div class="mb-3"><label class="col-form-label">Nomor Telepon</label><input type="text" class="form-control" name="phone_number" value="{{ $student->phone_number }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Alamat</label><textarea class="form-control" name="address" rows="3" required>{{ $student->address }}</textarea></div>
                                      <div class="mb-3"><label class="col-form-label">Tanggal Masuk</label><input type="date" class="form-control" name="admission_date" value="{{ $student->admission_date }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Nama Wali</label><input type="text" class="form-control" name="guardian_name" value="{{ $student->guardian_name }}" required></div>
                                      <div class="mb-3"><label class="col-form-label">Nomor Wali</label><input type="text" class="form-control" name="guardian_phone_number" value="{{ $student->guardian_phone_number }}" required></div>
                                      <div class="mb-3">
                                        <label class="col-form-label">Status</label>
                                        <select class="form-control" name="status_id" required>
                                          <option value="">-- Pilih Status --</option>
                                          @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}" {{ $status->id==$student->status_id?'selected':'' }}>{{ $status->status_name }}</option>
                                          @endforeach
                                        </select>
                                      </div>
                                      <div class="mb-3"><label class="col-form-label">Email</label><input type="email" class="form-control" name="email" value="{{ $student->email }}" required></div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                      <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                  </form>
                                </div></div>
                              </div>

                              {{-- Delete Modal --}}
                              <div class="modal fade" id="delete_data{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog"><div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Hapus: {{ $student->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">Yakin ingin menghapus <strong>{{ $student->name }}</strong>?</div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('student.destroy', $student->id) }}" method="POST">
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

<script>
function loadFile(event) {
  var output = document.getElementById('output');
  output.src = URL.createObjectURL(event.target.files[0]);
}
function loadFileUpdate(event, id) {
  var output = document.getElementById('outputUpdate' + id);
  if (output) output.src = URL.createObjectURL(event.target.files[0]);
}
</script>
@endsection
