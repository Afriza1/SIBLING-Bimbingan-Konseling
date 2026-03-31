@extends('layouts.dashboard')
@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h5 class="m-0 text-primary">Tabel Data Status</h5>
              @can('Tambah Status')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatusModal">
                Tambah Status
              </button>
              @endcan
              <div class="modal fade" id="addStatusModal" tabindex="-1" aria-labelledby="addStatusModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addStatusModalLabel">Tambah Status Baru</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form action="{{ route('status.store') }}" method="POST">
                        @csrf
                        <!-- Field Nama -->
                        <div class="mb-3">
                          <label for="status_name" class="col-form-label">Status</label>
                          <input type="text" class="form-control" id="status_name" name="status_name" required>
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
                            <th>Status</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($statuses as $status)
                            <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $status->status_name }}</td>
                              <td>
                                @can('Ubah Status')
                                <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#edit_data{{ $status->id }}">Edit</a>
                                @endcan
                                @can('Hapus Status')
                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                  data-bs-target="#delete_data{{ $status->id }}">Hapus</a>
                                @endcan
                                <!-- Edit Modal -->
                                <div class="modal fade" id="edit_data{{ $status->id }}" tabindex="-1"
                                  aria-labelledby="editModalLabel{{ $status->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $status->id }}">Edit Data:
                                          {{ $status->status_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('status.update', $status->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">

                                          <div class="mb-3">
                                            <label for="status_name" class="col-form-label">Status</label>
                                            <input type="text" class="form-control" id="status_name" name="status_name"
                                              value="{{ $status->status_name }}">
                                          </div>
                                          <!-- Add more fields as needed -->

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
                                <div class="modal fade" id="delete_data{{ $status->id }}" tabindex="-1"
                                  aria-labelledby="deleteModalLabel{{ $status->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $status->id }}">Hapus Data:
                                          {{ $status->status_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data {{ $status->status_name }}?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                          data-bs-dismiss="modal">Tutup</button>
                                        <form action="{{ route('status.destroy', $status->id) }}" method="POST">
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
                            <th>Status</th>
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
