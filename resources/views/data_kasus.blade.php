@extends('layouts.dashboard')
@section('content')
<style>
  .form-inline { display:flex; flex-wrap:nowrap; gap:15px; align-items:center; }
</style>
<div>
  <div class="content">
    <div class="row pt-4">
      <div class="mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0 text-primary">
              {{ $isSiswa ? 'Catatan Kasus Saya' : 'Tabel Data Kasus' }}
            </h5>

            @if($isSiswa)
              {{-- Siswa: hanya filter bulan & tahun --}}
              <form method="GET" action="{{ route('case.index') }}" class="d-flex gap-2 align-items-center">
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
              {{-- Non-siswa: filter lengkap --}}
              <div class="row filter-row">
                <form method="GET" action="{{ route('case.index') }}" class="form-inline">
                  <div class="col-12 col-sm-6 col-md-3">
                    <select name="class" class="form-select">
                      <option value="">Pilih Kelas</option>
                      @foreach($classes as $class)
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

              @can('Tambah Kasus')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah</button>
              <a href="{{ route('case.export') }}" class="btn btn-success">Ekspor</a>
              @endcan
            @endif

            {{-- MODAL TAMBAH (hanya non-siswa) --}}
            @if(!$isSiswa)
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Data Baru</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('case.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="col-form-label">Nama Siswa</label>
                      <select class="form-control" name="student_id" id="kasus_student_id" required onchange="updateKelasKasus(this)">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                          <option value="{{ $student->id }}"
                            data-kelas="{{ $student->class ? $student->class->class_level.' '.optional($student->class->major)->major_name.' '.$student->class->classroom : '-' }}">
                            {{ $student->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="col-form-label">Kelas</label>
                      <input type="text" class="form-control" id="kasus_kelas" readonly placeholder="Otomatis terisi setelah pilih siswa" style="background:#f8f9fa;">
                    </div>
                    <div class="mb-3"><label class="col-form-label">Kasus</label><input type="text" class="form-control" name="case_name" required></div>
                    <div class="mb-3"><label class="col-form-label">Poin Kasus</label><input type="number" class="form-control" name="case_point" required></div>
                    <div class="mb-3"><label class="col-form-label">Tanggal</label><input type="datetime-local" class="form-control" name="date" required></div>
                    <div class="mb-3"><label class="col-form-label">Keterangan</label><textarea class="form-control" name="description" required></textarea></div>
                    <div class="mb-3"><label class="col-form-label">Solusi</label><textarea class="form-control" name="resolution" required></textarea></div>
                    <div class="mb-3"><label class="col-form-label">Bukti</label><input type="file" class="form-control" name="evidence" accept=".pdf,.jpg,.png"></div>
                    <div class="mb-3">
                      <label class="col-form-label">Guru BK</label>
                      <select class="form-control" name="user_id" required>
                        <option value="">-- Pilih Guru BK --</option>
                        @foreach($users as $u)
                          @if($u->hasRole('Guru BK'))<option value="{{ $u->id }}">{{ $u->name }}</option>@endif
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
            @endif

          </div>{{-- end card-header --}}

          <div class="dt-container">
            <div class="row mt-2 justify-content-between">
              <div class="d-md-flex dt-layout-start col-md-auto me-auto"></div>
              <div class="d-md-flex dt-layout-end col-md-auto ms-auto"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card border-0 shadowNavbar">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="example" class="table table-hover" style="width:100%;--bs-table-bg:white;">
                      <thead class="text-nowrap table-light" style="--bs-table-bg:#eef2f7;--bs-table-border-color:#eef2f7;">
                        <tr>
                          <th>No</th>
                          @if(!$isSiswa)<th>Nama Siswa</th>@endif
                          <th>Kasus</th>
                          <th>Poin</th>
                          <th>Tanggal</th>
                          <th>Keterangan</th>
                          <th>Solusi</th>
                          <th>Bukti</th>
                          @if(!$isSiswa)<th>Guru BK</th><th>Aksi</th>@endif
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($cases as $case)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          @if(!$isSiswa)<td>{{ optional($case->student)->name }}</td>@endif
                          <td>{{ $case->case_name }}</td>
                          <td>{{ $case->case_point }}</td>
                          <td>{{ $case->date }}</td>
                          <td>{{ $case->description }}</td>
                          <td>{{ $case->resolution }}</td>
                          <td>
                            @if($case->evidence)
                            @php $ext = strtolower(pathinfo($case->evidence, PATHINFO_EXTENSION)); @endphp
                            @if($ext === 'pdf')
                                <a href="{{ asset('storage/' . $case->evidence) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0">PDF</a>
                            @else
                                <img src="{{ asset('storage/' . $case->evidence) }}"
                                style="max-width:80px;max-height:80px;border-radius:4px;cursor:zoom-in;"
                                onclick="showFullscreen('{{ asset('storage/' . $case->evidence) }}')"
                                title="Klik untuk lihat penuh">
                            @endif
                            @else
                            <span class="text-muted" style="font-size:12px;">Tidak ada</span>
                            @endif
                        </td>
                          @if(!$isSiswa)
                          <td>{{ optional($case->user)->name }}</td>
                          <td>
                            @can('Ubah Kasus')
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit_data{{ $case->id }}">Edit</a>
                            @endcan
                            @can('Hapus Kasus')
                            <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_data{{ $case->id }}">Hapus</a>
                            @endcan
                            {{-- Edit Modal --}}
                            <div class="modal fade" id="edit_data{{ $case->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog"><div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Edit: {{ $case->case_name }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('case.update', $case->id) }}" method="POST" enctype="multipart/form-data">
                                  @csrf @method('PUT')
                                  <div class="modal-body">
                                    <div class="mb-3">
                                      <label class="col-form-label">Nama Siswa</label>
                                      <select class="form-control" name="student_id" required>
                                        @foreach($students as $student)
                                          <option value="{{ $student->id }}" {{ $case->student_id==$student->id?'selected':'' }}>{{ $student->name }}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                    <div class="mb-3"><label class="col-form-label">Kasus</label><input type="text" class="form-control" name="case_name" value="{{ $case->case_name }}"></div>
                                    <div class="mb-3"><label class="col-form-label">Poin</label><input type="number" class="form-control" name="case_point" value="{{ $case->case_point }}"></div>
                                    <div class="mb-3"><label class="col-form-label">Tanggal</label><input type="datetime-local" class="form-control" name="date" value="{{ $case->date }}"></div>
                                    <div class="mb-3"><label class="col-form-label">Keterangan</label><textarea class="form-control" name="description">{{ $case->description }}</textarea></div>
                                    <div class="mb-3"><label class="col-form-label">Solusi</label><textarea class="form-control" name="resolution">{{ $case->resolution }}</textarea></div>
                                    <div class="mb-3"><label class="col-form-label">Bukti</label><input type="file" class="form-control" name="evidence" accept=".pdf,.jpg,.png"></div>
                                    <div class="mb-3">
                                      <label class="col-form-label">Guru BK</label>
                                      <select class="form-control" name="user_id" required>
                                        @foreach($users as $u)
                                          @if($u->hasRole('Guru BK'))<option value="{{ $u->id }}" {{ $case->user_id==$u->id?'selected':'' }}>{{ $u->name }}</option>@endif
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
                            <div class="modal fade" id="delete_data{{ $case->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog"><div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Hapus: {{ $case->case_name }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">Yakin ingin menghapus <strong>{{ $case->case_name }}</strong>?</div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                  <form action="{{ route('case.destroy', $case->id) }}" method="POST">
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
                        <tr><td colspan="{{ $isSiswa ? 7 : 10 }}" class="text-center py-4 text-muted">Belum ada catatan kasus</td></tr>
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
@push('scripts')
<script>
function updateKelasKasus(select) {
    var opt = select.options[select.selectedIndex];
    var kelas = opt ? (opt.getAttribute('data-kelas') || '-') : '-';
    var el = document.getElementById('kasus_kelas');
    if (el) el.value = kelas;
}
</script>
@endpush
@endsection
