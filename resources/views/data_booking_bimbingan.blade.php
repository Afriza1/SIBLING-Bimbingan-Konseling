@extends('layouts.dashboard')
@section('content')
    <div>
        <div class="content">
        <div class="row pt-4">
            <div class="mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 text-primary">Tabel Data Booking Bimbingan</h5>
                @can('Tambah Booking Bimbingan')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Tambah Data
                </button>
                @endcan
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah Data Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <form action="{{ route('guidanceBooking.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Field Nama -->
                            <div class="form-group">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" id="name" name="name" class="form-control"
                                required />
                            </div>
                            <div class="form-group mt-3">
                                <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                                <input type="number" id="phone_number" name="phone_number" class="form-control"
                                required />
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group mt-3">
                                    <label for="booking_date" class="form-label">Pilih Tanggal</label>
                                    <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="form-group mt-3">
                                    <label for="booking_time" class="form-label">Pilih Waktu</label>
                                    <select id="booking_time" name="booking_time" class="form-control @error('booking_time') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Waktu</option>
                                    @php
                                        $timeSlots = ['09:00', '09:15', '11:30', '11:45'];
                                        $maxBookingPerSlot = 3; // Maksimal booking per slot

                                        foreach ($timeSlots as $time) {
                                        $bookedCount = \App\Models\GuidanceBooking::where('booking_date', now()->format('Y-m-d') . " $time:00")->count();
                                        $remainingSlots = $maxBookingPerSlot - $bookedCount;
                                        $disabled = $remainingSlots <= 0 ? 'disabled' : '';
                                        echo "<option value='$time' $disabled>$time - Sisa $remainingSlots orang</option>";
                                        }
                                    @endphp
                                    </select>
                                    @error('booking_time')
                                    <div  class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="pending" selected>Belum Dikonfirmasi</option>
                                    <option value="confirmed">Terkonfirmasi</option>
                                    <option value="completed">Selesai</option>
                                </select>
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
                                    <th>Nama Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Nomor WhatsApp</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guidanceBookings as $guidanceBooking)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $guidanceBooking->name }}</td>
                                        <td>{{ $guidanceBooking->booking_date }}</td>
                                        <td>{{ $guidanceBooking->phone_number }}</td>
                                        <td>
                                            <span class="badge
                                                {{ $guidanceBooking->status == 'pending' ? 'bg-warning text-dark' : '' }}
                                                {{ $guidanceBooking->status == 'confirmed' ? 'bg-success' : '' }}
                                                {{ $guidanceBooking->status == 'completed' ? 'bg-dark' : '' }}">
                                                {{ $guidanceBooking->status == 'pending' ? 'Belum Dikonfirmasi' : ($guidanceBooking->status == 'confirmed' ? 'Terkonfirmasi' : 'Selesai') }}
                                            </span>
                                        </td>
                                        <td>
                                        @can('Ubah Booking Bimbingan')
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#edit_data{{ $guidanceBooking->id }}">Edit</a>
                                        @endcan
                                        @can('Hapus Booking Bimbingan')
                                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#delete_data{{ $guidanceBooking->id }}">Hapus</a>
                                        @endcan
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="edit_data{{ $guidanceBooking->id }}" tabindex="-1"
                                            aria-labelledby="editModalLabel{{ $guidanceBooking->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $guidanceBooking->id }}">Edit Data:
                                                    {{ $guidanceBooking->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('guidanceBooking.update', $guidanceBooking->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="name" class="form-label">Nama</label>
                                                        <input type="text" id="name" name="name" class="form-control" value="{{ $guidanceBooking->name }}" required />
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                                                        <input type="number" id="phone_number" name="phone_number" class="form-control"
                                                            value="{{ $guidanceBooking->phone_number }}" required />
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <label for="booking_date" class="form-label">Pilih Tanggal</label>
                                                                <input type="date" id="booking_date" name="booking_date" class="form-control" value="{{ old('booking_date', explode(' ', $guidanceBooking->booking_date)[0]) }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <label for="booking_time" class="form-label">Pilih Waktu</label>
                                                                <select id="booking_time" name="booking_time" class="form-control" required>
                                                                    <option value="" disabled>Pilih Waktu</option>
                                                                    <option value="09:00" @selected(old('booking_time', substr($guidanceBooking->booking_date, 11, 5)) == '09:00')>09:00 - 09:15</option>
                                                                    <option value="09:15" @selected(old('booking_time', substr($guidanceBooking->booking_date, 11, 5)) == '09:15')>09:15 - 09:30</option>
                                                                    <option value="11:30" @selected(old('booking_time', substr($guidanceBooking->booking_date, 11, 5)) == '11:30')>11:30 - 11:45</option>
                                                                    <option value="11:45" @selected(old('booking_time', substr($guidanceBooking->booking_date, 11, 5)) == '11:45')>11:45 - 12:00</option>
                                                                </select>
                                                                @error('booking_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select id="status" name="status" class="form-control" required>
                                                            <option value="pending" {{ $guidanceBooking->status == 'pending' ? 'selected' : '' }}>Belum Dikonfirmasi</option>
                                                            <option value="confirmed" {{ $guidanceBooking->status == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                                                            <option value="completed" {{ $guidanceBooking->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                                </form>
                                            </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="delete_data{{ $guidanceBooking->id }}" tabindex="-1"
                                            aria-labelledby="deleteModalLabel{{ $guidanceBooking->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $guidanceBooking->id }}">Hapus Data:
                                                    {{ $guidanceBooking->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus data {{ $guidanceBooking->topics }}?
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <form action="{{ route('guidanceBooking.destroy', $guidanceBooking->id) }}" method="POST">
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
                                    <th>Nama Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Nomor WhatsApp</th>
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
        <div class="row gx-4 pt-4">
        <div class="col-lg-9">
        </div>
        <div class="col-lg-3 m-0"></div>
        </div>
    </div>
@endsection
