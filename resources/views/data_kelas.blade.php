@extends('layouts.dashboard')
@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h5 class="m-0 text-primary">Tabel Data Kelas</h5>
              @can('Tambah Kelas')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                Tambah Kelas
              </button>
              @endcan
              <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addClassModalLabel">Tambah Kelas Baru</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form action="{{ route('class.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                          <label for="class_level" class="col-form-label">Kelas</label>
                          <input type="text" class="form-control" id="class_level" name="class_level" required>
                        </div>
                        <div class="mb-3">
                          <label for="major_id" class="col-form-label">Jurusan</label>
                          <select class="form-control" id="major_id" name="major_id" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($majors as $major)
                              <option value="{{ $major->id }}">{{ $major->major_name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="classroom" class="col-form-label">Ruang</label>
                          <input type="text" class="form-control" id="classroom" name="classroom" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                    </form>
                  </div>
                </div>
              </div>
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
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Ruang</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($classes as $class)
                            <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $class->class_level }}</td>
                              <td>{{ $class->major->major_name }}</td>
                              <td>{{ $class->classroom }}</td>
                              <td>
                                @can('Ubah Kelas')
                                <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#edit_data{{ $class->id }}">Edit</a>
                                @endcan
                                @can('Hapus Kelas')
                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#delete_data{{ $class->id }}">Hapus</a>
                                @endcan
                                <!-- Edit Modal -->
                                <div class="modal fade" id="edit_data{{ $class->id }}" tabindex="-1"
                                  aria-labelledby="editModalLabel{{ $class->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $class->id }}">Edit Data:
                                          {{ $class->class_level }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('class.update', $class->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                          <div class="mb-3">
                                            <label for="class_level" class="col-form-label">Kelas</label>
                                            <input type="text" class="form-control" id="class_level" name="class_level"
                                              value="{{ $class->class_level }}">
                                          </div>
                                          <div class="mb-3">
                                            <label for="major_id" class="col-form-label">Jurusan</label>
                                            <select class="form-control" id="manjor_id" name="major_id" required>
                                              <option value="">-- Pilih Kelas --</option>
                                              @foreach ($majors as $major)
                                                <option value="{{ $major->id }}"
                                                  {{ $class->major_id == $major->id ? 'selected' : '' }}>
                                                  {{ $major->major_name }}
                                                </option>
                                              @endforeach
                                            </select>
                                          </div>
                                          <div class="mb-3">
                                            <label for="classroom" class="col-form-label">Kelas</label>
                                            <input type="text" class="form-control" id="classroom" name="classroom"
                                              value="{{ $class->classroom }}">
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

                                <!-- Delete Modal -->
                                <div class="modal fade" id="delete_data{{ $class->id }}" tabindex="-1"
                                  aria-labelledby="deleteModalLabel{{ $class->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $class->id }}">Hapus Data:
                                          {{ $class->class_level }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data {{ $class->class }}?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                          data-bs-dismiss="modal">Tutup</button>
                                        <form action="{{ route('class.destroy', $class->id) }}" method="POST">
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
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Ruang</th>
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
    <div class="row gx-4 pt-4">
      <div class="col-lg-9">
      </div>
      <div class="col-lg-3 m-0"></div>
    </div>
  </div>
@endsection
