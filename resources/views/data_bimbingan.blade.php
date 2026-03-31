@extends('layouts.dashboard')
@section('content')
<style>
  .form-inline { display:flex; flex-wrap:nowrap; gap:15px; align-items:center; }
  .form-inline .form-group { flex:1; min-width:200px; }
</style>
<div>
  <div class="content">
    <div class="row pt-4">
      <div class="mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0 text-primary">
              {{ $isSiswa ? 'Riwayat Bimbingan Saya' : 'Tabel Data Bimbingan' }}
            </h5>

            {{-- FILTER --}}
            @if($isSiswa)
              {{-- Siswa: hanya filter bulan & tahun --}}
              <form method="GET" action="{{ route('guidance.index') }}" class="d-flex gap-2 align-items-center">
                <select name="month" class="form-select form-select-sm">
                  @for($m=1;$m<=12;$m++)
                    <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ $selectedMonth==str_pad($m,2,'0',STR_PAD_LEFT)?'selected':'' }}>
                      {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                  @endfor
                </select>
                <select name="year" class="form-select form-select-sm">
                  @foreach($years as $y)<option value="{{ $y }}" {{ $selectedYear==$y?'selected':'' }}>{{ $y }}</option>@endforeach
                </select>
                <button type="submit" class="btn btn-success btn-sm"><i class="uil uil-search"></i></button>
              </form>
            @else
              {{-- Non-siswa: filter kelas, bulan, tahun --}}
              <div class="row filter-row">
                <form method="GET" action="{{ route('guidance.index') }}" class="form-inline">
                  <div class="col-12 col-sm-6 col-md-3">
                    <select name="class" class="form-select">
                      <option value="">Pilih Kelas</option>
                      @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass==$class->id?'selected':'' }}>
                          {{ $class->class_level }} {{ $class->major->major_name }} {{ $class->classroom }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-6 col-sm-4 col-md-2">
                    <select name="month" class="form-select">
                      @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val=>$label)
                        <option value="{{ $val }}" {{ $selectedMonth==$val?'selected':'' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-6 col-sm-4 col-md-2">
                    <select name="year" class="form-select">
                      @foreach($years as $y)<option value="{{ $y }}" {{ $selectedYear==$y?'selected':'' }}>{{ $y }}</option>@endforeach
                    </select>
                  </div>
                  <button type="submit" class="btn btn-success btn-sm"><i class="uil uil-search"></i></button>
                </form>
              </div>

              {{-- Tombol tambah hanya untuk non-siswa --}}
              @can('Tambah Bimbingan')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah</button>
              <a href="{{ route('guidance.export') }}" class="btn btn-success">Ekspor</a>
              @endcan
            @endif

            {{-- MODAL TAMBAH (hanya untuk non-siswa) --}}
            @if(!$isSiswa)
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Data Bimbingan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('guidance.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="booking_id" id="selected_booking_id" value="">

                  <div class="modal-body">

                    {{-- TAB PILIHAN --}}
                    <ul class="nav nav-tabs mb-3" id="tambahTab">
                      <li class="nav-item">
                        <a class="nav-link active" id="tab-booking" href="#" onclick="switchTab('booking')">
                          📋 Dari Booking Siswa
                          @if($confirmedBookings->count() > 0)
                            <span class="badge bg-danger ms-1">{{ $confirmedBookings->count() }}</span>
                          @endif
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-manual" href="#" onclick="switchTab('manual')">
                          ✏️ Input Manual
                        </a>
                      </li>
                    </ul>

                    {{-- PANEL: DARI BOOKING --}}
                    <div id="panel-booking">
                      @if($confirmedBookings->count() > 0)
                        <div class="mb-3">
                          <label class="form-label fw-bold">Pilih Siswa yang Sudah Booking</label>
                          <div class="list-group" style="max-height:200px;overflow-y:auto;">
                            @foreach($confirmedBookings as $booking)
                            <button type="button"
                              class="list-group-item list-group-item-action booking-item"
                              data-booking-id="{{ $booking->id }}"
                              data-name="{{ $booking->name }}"
                              data-date="{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}"
                              onclick="pilihBooking(this)">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <strong>{{ $booking->name }}</strong>
                                  <small class="text-muted d-block">
                                    📅 {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y, H:i') }}
                                    @if($booking->phone_number)
                                      &nbsp;|&nbsp; 📱 {{ $booking->phone_number }}
                                    @endif
                                  </small>
                                </div>
                                <span class="badge bg-success">Confirmed</span>
                              </div>
                            </button>
                            @endforeach
                          </div>
                        </div>
                        <div id="booking-selected-info" class="alert alert-info d-none">
                          Siswa terpilih: <strong id="booking-selected-name"></strong>
                          &nbsp;|&nbsp; Tanggal booking: <strong id="booking-selected-date"></strong>
                        </div>
                      @else
                        <div class="alert alert-warning">
                          Tidak ada booking dengan status <strong>Confirmed</strong> saat ini.<br>
                          Konfirmasi booking siswa terlebih dahulu di menu <strong>Booking Bimbingan</strong>, atau gunakan tab <strong>Input Manual</strong>.
                        </div>
                      @endif
                    </div>

                    {{-- PANEL: MANUAL --}}
                    <div id="panel-manual" style="display:none;">
                      <div class="mb-3">
                        <label class="col-form-label">Nama Siswa</label>
                        <select class="form-control" name="student_id" id="manual_student_id" onchange="updateKelasManual(this)">
                          <option value="">-- Pilih Siswa --</option>
                          @foreach(App\Models\Student::with('class.major')->orderBy('name')->get() as $s)
                            <option value="{{ $s->id }}"
                              data-kelas="{{ $s->class ? $s->class->class_level.' '.optional($s->class->major)->major_name.' '.$s->class->classroom : '-' }}">
                              {{ $s->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Kelas</label>
                        <input type="text" class="form-control" id="manual_kelas" readonly placeholder="Otomatis terisi setelah pilih siswa" style="background:#f8f9fa;">
                      </div>
                    </div>

                    {{-- FIELD BERSAMA (booking & manual) --}}
                    <div id="shared-fields" class="{{ $confirmedBookings->count() > 0 ? 'd-none' : '' }}">
                      <input type="hidden" name="student_id" id="booking_student_id" value="">
                      <div class="mb-3">
                        <label class="col-form-label">Topik Bimbingan</label>
                        <input type="text" class="form-control" name="topics" required placeholder="Contoh: Konseling karir, Masalah belajar...">
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Tanggal Bimbingan</label>
                        <input type="date" class="form-control" name="date" id="bimbingan_date" required>
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Guru BK</label>
                        <select class="form-control" name="user_id" required>
                          <option value="">-- Pilih Guru BK --</option>
                          @foreach($users as $u)
                            @if($u->hasRole('Guru BK'))<option value="{{ $u->id }}">{{ $u->name }}</option>@endif
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Catatan</label>
                        <textarea class="form-control" name="notes" required rows="3" placeholder="Hasil dan catatan bimbingan..."></textarea>
                      </div>
                      <div class="mb-3">
                        <label class="col-form-label">Bukti Bimbingan <small class="text-muted">(opsional)</small></label>
                        <input type="file" class="form-control" name="proof_of_guidance" accept=".pdf,.jpg,.png">
                      </div>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan" {{ $confirmedBookings->count() > 0 ? 'disabled' : '' }}>Simpan Data</button>
                  </div>
                </form>
              </div></div>
            </div>

            <script>
            var currentTab = 'booking';

            function switchTab(tab) {
                currentTab = tab;
                document.getElementById('tab-booking').classList.toggle('active', tab === 'booking');
                document.getElementById('tab-manual').classList.toggle('active', tab === 'manual');
                document.getElementById('panel-booking').style.display = tab === 'booking' ? '' : 'none';
                document.getElementById('panel-manual').style.display  = tab === 'manual'  ? '' : 'none';

                if (tab === 'manual') {
                    // Tampilkan shared fields, pakai student_id dari select manual
                    document.getElementById('shared-fields').classList.remove('d-none');
                    document.getElementById('booking_student_id').name = ''; // nonaktifkan hidden
                    document.getElementById('manual_student_id').name  = 'student_id'; // aktifkan select
                    document.getElementById('selected_booking_id').value = '';
                    document.getElementById('btn-simpan').disabled = false;
                } else {
                    // Reset ke mode booking
                    document.getElementById('manual_student_id').name  = '';
                    document.getElementById('booking_student_id').name = 'student_id';
                    // Sembunyikan shared fields sampai booking dipilih
                    var hasConfirmed = {{ $confirmedBookings->count() > 0 ? 'true' : 'false' }};
                    if (hasConfirmed) {
                        document.getElementById('shared-fields').classList.add('d-none');
                        document.getElementById('btn-simpan').disabled = true;
                        // Reset pilihan
                        document.querySelectorAll('.booking-item').forEach(el => el.classList.remove('active'));
                        document.getElementById('booking-selected-info').classList.add('d-none');
                        document.getElementById('booking_student_id').value = '';
                        document.getElementById('selected_booking_id').value = '';
                    }
                }
            }

            function updateKelasManual(select) {
                var opt = select.options[select.selectedIndex];
                var kelas = opt ? (opt.getAttribute('data-kelas') || '-') : '-';
                var kelasInput = document.getElementById('manual_kelas');
                if (kelasInput) kelasInput.value = kelas;
            }

            function pilihBooking(el) {
                // Highlight pilihan
                document.querySelectorAll('.booking-item').forEach(e => e.classList.remove('active'));
                el.classList.add('active');

                var bookingId = el.dataset.bookingId;
                var name      = el.dataset.name;
                var date      = el.dataset.date;

                // Cari student_id berdasarkan nama
                fetch('/api/student-by-name?name=' + encodeURIComponent(name))
                    .then(r => r.json())
                    .then(data => {
                        if (data.id) {
                            document.getElementById('booking_student_id').value = data.id;
                        }
                    })
                    .catch(() => {});

                document.getElementById('selected_booking_id').value = bookingId;
                document.getElementById('bimbingan_date').value      = date;
                document.getElementById('booking-selected-name').textContent = name;
                document.getElementById('booking-selected-date').textContent = date;
                document.getElementById('booking-selected-info').classList.remove('d-none');

                // Tampilkan shared fields
                document.getElementById('shared-fields').classList.remove('d-none');
                document.getElementById('btn-simpan').disabled = false;
                document.getElementById('manual_student_id').name  = '';
                document.getElementById('booking_student_id').name = 'student_id';
            }
            </script>
            @endif

          </div>{{-- end card-header --}}

          <div class="dt-container">
            <div class="row mt-2 justify-content-between">
              <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto"></div>
              <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card border-0 shadowNavbar" id="panel">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="example" class="table table-hover" style="width:100%; --bs-table-bg:white;">
                      <thead class="text-nowrap table-light" style="--bs-table-bg:#eef2f7;--bs-table-border-color:#eef2f7;">
                        <tr>
                          <th>No</th>
                          @if(!$isSiswa)<th>Nama Siswa</th>@endif
                          <th>Topik</th>
                          <th>Tanggal</th>
                          <th>Guru BK</th>
                          <th>Catatan</th>
                          <th>Bukti</th>
                          <th>Ke-</th>
                          @if(!$isSiswa)<th>Aksi</th>@endif
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($guidances as $guidance)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          @if(!$isSiswa)<td>{{ optional($guidance->student)->name ?? '<em class="text-muted">Terhapus</em>' }}</td>@endif
                          <td>{{ $guidance->topics }}</td>
                          <td>{{ $guidance->date }}</td>
                          <td>{{ optional($guidance->user)->name ?? '-' }}</td>
                          <td>{{ $guidance->notes }}</td>
                          <td>
                            @if($guidance->proof_of_guidance)
                            @php $ext = strtolower(pathinfo($guidance->proof_of_guidance, PATHINFO_EXTENSION)); @endphp
                            @if($ext === 'pdf')
                                <a href="{{ asset('storage/'.$guidance->proof_of_guidance) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0">PDF</a>
                            @else
                                <img src="{{ asset('storage/'.$guidance->proof_of_guidance) }}"
                                style="max-width:80px;max-height:80px;border-radius:4px;cursor:zoom-in;"
                                onclick="showFullscreen('{{ asset('storage/'.$guidance->proof_of_guidance) }}')"
                                title="Klik untuk lihat penuh">
                            @endif
                            @else
                            <span class="text-muted" style="font-size:12px;">Tidak ada</span>
                            @endif
                        </td>
                          <td>{{ $guidance->guidance_count }}</td>
                          @if(!$isSiswa)
                          <td>
                            @can('Ubah Bimbingan')
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit_data{{ $guidance->id }}">Edit</a>
                            @endcan
                            @can('Hapus Bimbingan')
                            <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_data{{ $guidance->id }}">Hapus</a>
                            @endcan
                            {{-- Edit Modal --}}
                            <div class="modal fade" id="edit_data{{ $guidance->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog"><div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Edit: {{ $guidance->topics }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('guidance.update', $guidance->id) }}" method="POST" enctype="multipart/form-data">
                                  @csrf @method('PUT')
                                  <div class="modal-body">
                                    <div class="mb-3">
                                      <label class="col-form-label">Nama Siswa</label>
                                      <select class="form-control" name="student_id" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($students as $student)
                                          <option value="{{ $student->id }}" {{ $guidance->student_id==$student->id?'selected':'' }}>{{ $student->name }}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                    <div class="mb-3"><label class="col-form-label">Topik</label><input type="text" class="form-control" name="topics" value="{{ $guidance->topics }}"></div>
                                    <div class="mb-3"><label class="col-form-label">Tanggal</label><input type="date" class="form-control" name="date" value="{{ $guidance->date }}"></div>
                                    <div class="mb-3">
                                      <label class="col-form-label">Guru BK</label>
                                      <select class="form-control" name="user_id" required>
                                        <option value="">-- Pilih Guru BK --</option>
                                        @foreach($users as $u)
                                          @if($u->hasRole('Guru BK'))<option value="{{ $u->id }}" {{ $guidance->user_id==$u->id?'selected':'' }}>{{ $u->name }}</option>@endif
                                        @endforeach
                                      </select>
                                    </div>
                                    <div class="mb-3"><label class="col-form-label">Catatan</label><textarea class="form-control" name="notes">{{ $guidance->notes }}</textarea></div>
                                    <div class="mb-3"><label class="col-form-label">Bukti</label><input type="file" class="form-control" name="proof_of_guidance" accept=".pdf,.jpg,.png"></div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                  </div>
                                </form>
                              </div></div>
                            </div>
                            {{-- Delete Modal --}}
                            <div class="modal fade" id="delete_data{{ $guidance->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog"><div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Hapus: {{ $guidance->topics }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">Yakin ingin menghapus <strong>{{ $guidance->topics }}</strong>?</div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                  <form action="{{ route('guidance.destroy', $guidance->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                  </form>
                                </div>
                              </div></div>
                            </div>
                          </td>
                          @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ $isSiswa ? 7 : 9 }}" class="text-center py-4 text-muted">Belum ada data bimbingan</td></tr>
                        @endforelse
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
