@extends('layouts.dashboard')
@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h5 class="m-0 text-primary">Tabel Data User</h5>
            </div>
            <div class="dt-container">
              <div class="row mt-2 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto"></div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto"></div>
              </div>
            </div>
            <div class="row ">
              <div class="col-lg-12">
                <div class="card border-0 shadowNavbar" id="panel">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="example" class="table table-hover" style="width:100%; --bs-table-bg: white;">
                        <thead class="text-nowrap table-light rounded-header"
                          style="--bs-table-bg: #eef2f7; --bs-table-border-color: #eef2f7;">
                          <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP/NISN</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Agama</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($users as $user)
                            <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>
                                @if ($user->photo)
                                  @php
                                    $fileType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $user->photo);
                                  @endphp
                                  @if ($fileType === 'application/pdf')
                                    <i class="uil uil-file-alt" style="font-size: 50px; color: red; margin-bottom: 10px;"></i>
                                  @else
                                    <img src="{{ route('user.showImage', $user->id) }}" alt="Foto" style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%; margin-bottom: 10px;">
                                  @endif
                                  <a href="{{ route('user.download', $user->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-download-alt"></i> Unduh
                                  </a>
                                @else
                                  Tidak ada foto
                                @endif
                              </td>
                              <td>{{ $user->name }}</td>
                              <td>{{ $user->nip }}</td>
                              <td>{{ $user->gender }}</td>
                              <td>{{ $user->place_of_birth }}</td>
                              <td>{{ $user->date_of_birth }}</td>
                              <td>{{ $user->religion }}</td>
                              <td>{{ $user->phone_number }}</td>
                              <td>{{ $user->address }}</td>
                              <td>{{ $user->email }}</td>
                              <td>
                                <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#edit_data{{ $user->id }}">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#delete_data{{ $user->id }}">Hapus</a>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="edit_data{{ $user->id }}" tabindex="-1"
                                  aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $user->id }}">Edit Data:
                                          {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                          <div style="display: flex; justify-content: center; align-items: center;">
                                            <img id="outputUpdate" src="{{ route('user.showImage', $user->id) }}" alt="Foto" style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%; margin-bottom: 10px;">
                                          </div>
                                          <div class="mb-3">
                                            <label for="photo" class="col-form-label">Unggah Foto</label>
                                              @if ($user->photo)
                                                <div class="mb-2">
                                                  <p>File yang sudah ada:</p>
                                                  @php
                                                      $fileType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $user->photo);
                                                  @endphp
                                                  @if (str_contains($fileType, 'image'))
                                                      <img src="{{ route('user.showImage', $user->id) }}" alt="Foto" style="max-width: 100px; max-height: 100px; margin-bottom: 10px;">
                                                  @elseif ($fileType === 'application/pdf')
                                                      <i class="uil uil-file-pdf-alt" style="font-size: 50px;"></i>
                                                  @else
                                                      <i class="uil uil-file-alt" style="font-size: 50px;"></i>
                                                  @endif
                                                  <br>
                                                  <a href="{{ route('user.download', $user->id) }}" class="btn btn-sm btn-primary mt-2">Download Foto</a>
                                                </div>
                                                <p class="text-muted">Jika tidak ingin mengganti file, biarkan kosong.</p>
                                              @else
                                                <p class="text-danger">Belum ada Foto yang diunggah.</p>
                                              @endif
                                            <input type="file" class="form-control" id="photo" name="photo" accept=".pdf,.jpg,.png" onchange="loadFileUpdate(event)">
                                          </div>
                                          <div class="mb-3">
                                            <label for="name" class="col-form-label">Nama:</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                              value="{{ $user->name }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="nip" class="col-form-label">NIP/NUPTK</label>
                                            <input type="text" class="form-control" id="nip" name="nip"
                                              value="{{ $user->nip }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="gender" class="col-form-label">Jenis Kelamin</label>
                                            <select class="form-control" id="gender" name="gender">
                                              <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                              <option value="Laki-laki" {{ $user->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                              <option value="Perempuan" {{ $user->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                          </div>
                                          <div class="mb-3">
                                            <label for="place_of_birth" class="col-form-label">Tempat Lahir</label>
                                            <input type="text" class="form-control" id="place_of_birth" name="place_of_birth"
                                              value="{{ $user->place_of_birth }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="date_of_birth" class="col-form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                              value="{{ $user->date_of_birth }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="religion" class="col-form-label">Agama</label>
                                            <select class="form-control" id="religion" name="religion">
                                              <option value="" selected disabled>Pilih Agama</option>
                                              <option value="Islam" {{ $user->religion == 'Islam' ? 'selected' : '' }}>Islam
                                              </option>
                                              <option value="Kristen" {{ $user->religion == 'Kristen' ? 'selected' : '' }}>
                                                Kristen</option>
                                              <option value="Katolik" {{ $user->religion == 'Katolik' ? 'selected' : '' }}>
                                                Katolik</option>
                                              <option value="Hindu" {{ $user->religion == 'Hindu' ? 'selected' : '' }}>Hindu
                                              </option>
                                              <option value="Buddha" {{ $user->religion == 'Buddha' ? 'selected' : '' }}>
                                                Buddha
                                              </option>
                                              <option value="Konghucu" {{ $user->religion == 'Konghucu' ? 'selected' : '' }}>
                                                Konghucu</option>
                                            </select>
                                          </div>
                                          <div class="mb-3">
                                            <label for="phone_number" class="col-form-label">Nomor Telepon</label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                              value="{{ $user->phone_number }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="address" class="col-form-label">Alamat</label>
                                            <input type="textarea" class="form-control" id="address" name="address"
                                              value="{{ $user->address }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="email" class="col-form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                              value="{{ $user->email }}">
                                          </div>

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

                                <!-- Fullscreen Image Preview -->
                                <div id="fullscreenPreview" onclick="closeFullscreen()">
                                  <span class="close-preview">&times;</span>
                                  <img src="" alt="Fullscreen preview">
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="delete_data{{ $user->id }}" tabindex="-1"
                                  aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">Hapus Data:
                                          {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data {{ $user->name }}?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                          data-bs-dismiss="modal">Tutup</button>
                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </td>
                          @endforeach
                        </tbody>
                        {{-- <tfoot>
                          <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP/NUPTK</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Agama</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th>Email</th>
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
      </div>
    </div>
  </div>
@endsection
<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);

  }
  var loadFileUpdate = function(event) {
    var outputUpdate = document.getElementById('outputUpdate');
    outputUpdate.src = URL.createObjectURL(event.target.files[0]);

  }
</script>

