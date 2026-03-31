@extends('layouts.dashboard')
@section('content')
<style>
    .form-inline { display:flex; flex-wrap:nowrap; gap:15px; align-items:center; }
    .hiddenRow { padding:0 !important; }
    .expand-toggle { cursor:pointer; }
    .expand-box { padding:12px; border-radius:6px; background-color:#f8f9fa; }
    .status-cell { font-weight:600; }
    .btn-save-sim { min-width:120px; }
    .evidence-preview img { max-width:80px; max-height:80px; object-fit:cover; border-radius:4px; cursor:pointer; }
</style>

<div>
  <div class="content">
    <div class="row pt-4">
      <div class="mb-4">
        <div class="card shadow mb-4">

          {{-- ===== CARD HEADER ===== --}}
          <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0 text-primary">
              {{ $isSiswa ? 'Rekap Absensi Saya' : 'Tabel Absensi Siswa' }}
            </h5>

            @if(!$isSiswa && !$isWaliKelas)
            <div class="row filter-row">
              <form method="GET" action="{{ route('attendance.index') }}" class="form-inline" id="filterForm">
                <div class="col-12 col-sm-6 col-md-3">
                  <select name="class" class="form-select" onchange="this.form.submit()">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $class)
                      <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                        {{ $class->class_level }} {{ $class->major->major_name }} {{ $class->classroom }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @if ($view === 'daily')
                  <div class="col-6 col-sm-4 col-md-3">
                    <input type="date" id="selectedDate" name="date" class="form-control"
                      value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                  </div>
                @endif
                @if ($view === 'monthly')
                  <div class="col-6 col-sm-3 col-md-2">
                    <select name="month" id="selectedMonth" class="form-select">
                      @foreach (range(1,12) as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                          {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-6 col-sm-3 col-md-2">
                    <select name="year" id="selectedYear" class="form-select">
                      @foreach ($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
                @if ($view === 'semester')
                  <div class="col-6 col-sm-3 col-md-2">
                    <select name="year" id="selectedYear" class="form-select">
                      @foreach ($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
                <input type="hidden" name="view" value="{{ $view }}">
              </form>
            </div>
            @endif

            @if($isWaliKelas)
            <div class="d-flex gap-2 align-items-center">
              <form method="GET" action="{{ route('attendance.index') }}" class="d-flex gap-2 align-items-center" id="filterFormWK">
                @if($view === 'daily')
                  <input type="date" name="date" class="form-control form-control-sm"
                    value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                    onchange="this.form.submit()">
                @endif
                @if($view === 'monthly')
                  <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(range(1,12) as $m)
                      <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                      </option>
                    @endforeach
                  </select>
                  <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($years as $y)
                      <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                @endif
                @if($view === 'semester')
                  <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($years as $y)
                      <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                @endif
                <input type="hidden" name="view" value="{{ $view }}">
              </form>
            </div>
            @endif

            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('attendance.index', ['view'=>'daily','class'=>$selectedClass,'month'=>$selectedMonth,'year'=>$selectedYear]) }}"
                class="btn btn-outline-primary btn-sm {{ $view=='daily'?'active':'' }}">Harian</a>
              <a href="{{ route('attendance.index', ['view'=>'monthly','class'=>$selectedClass,'month'=>$selectedMonth,'year'=>$selectedYear]) }}"
                class="btn btn-outline-primary btn-sm {{ $view=='monthly'?'active':'' }}">Bulanan</a>
              <a href="{{ route('attendance.index', ['view'=>'semester','class'=>$selectedClass,'year'=>$selectedYear]) }}"
                class="btn btn-outline-primary btn-sm {{ $view=='semester'?'active':'' }}">Semester</a>
              @if ($view == 'semester')
                <form method="GET" action="{{ route('attendance.index') }}" class="d-inline-block ms-2">
                  <input type="hidden" name="class" value="{{ $selectedClass }}">
                  <input type="hidden" name="year" value="{{ $selectedYear }}">
                  <input type="hidden" name="view" value="semester">
                  <select name="semester" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="ganjil" {{ request('semester','ganjil')=='ganjil'?'selected':'' }}>Semester Ganjil</option>
                    <option value="genap" {{ request('semester')=='genap'?'selected':'' }}>Semester Genap</option>
                  </select>
                </form>
              @endif
            </div>
          </div>

          @if(!$isSiswa && !$isWaliKelas)
          <div class="px-3 pt-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
              + Tambah Siswa
            </button>
          </div>
          <div class="modal fade" id="addStudentModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="POST" action="{{ route('attendance.addStudent') }}">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <input type="text" name="name" class="form-control" required>
                  </div>
                  <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-success">Simpan</button>
                </div>
              </form>
            </div></div>
          </div>
          @endif

          <div class="card-body">
            <div class="table-responsive">

              {{-- ===== DAILY ===== --}}
              @if ($view == 'daily')
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width:60px">No</th>
                    <th>Nama Siswa</th>
                    <th style="width:160px">Status</th>
                    <th style="width:100px">Bukti</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($students as $i => $student)
                    @php $att = $attendances->firstWhere('student_id', $student->id); @endphp
                    <tr class="student-row" data-student-id="{{ $student->id }}">
                      <td>{{ $i+1 }}</td>
                      <td class="{{ !$isSiswa ? 'expand-toggle text-primary' : '' }}"
                          style="{{ !$isSiswa ? 'cursor:pointer;' : '' }}"
                          title="{{ !$isSiswa ? 'Klik untuk input' : '' }}">
                        {{ $student->name }}
                      </td>
                      <td class="status-cell">{{ $att->presence_status ?? '-' }}</td>
                      <td class="evidence-preview">
                        @if($att && $att->evidence)
                          @php $ext = strtolower(pathinfo($att->evidence, PATHINFO_EXTENSION)); @endphp
                          @if($ext === 'pdf')
                            <a href="{{ asset('storage/' . $att->evidence) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                              <i class="uil uil-file-pdf-alt"></i> PDF
                            </a>
                          @else
                            <img src="{{ asset('storage/' . $att->evidence) }}" alt="Bukti"
                              data-bs-toggle="modal" data-bs-target="#evidenceModal"
                              data-src="{{ asset('storage/' . $att->evidence) }}"
                              style="max-width:60px;max-height:60px;object-fit:cover;border-radius:4px;cursor:pointer;">
                          @endif
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                    </tr>

                    @if(!$isSiswa)
                    <tr class="expand-row d-none" id="expand-d-{{ $student->id }}">
                      <td colspan="4" class="hiddenRow">
                        <div class="expand-box">
                          <div class="row">
                            <div class="col-md-8">
                              <div class="mb-2">
                                <strong>Tanggal:</strong>
                                <span>{{ request('date', \Carbon\Carbon::today()->translatedFormat('d F Y')) }}</span>
                              </div>
                              <div class="mb-2">
                                <strong>Pilih Kehadiran:</strong><br>
                                @foreach(['Hadir','Ijin','Sakit','Alpa'] as $s)
                                  <label class="me-3">
                                    <input type="radio" name="status-{{ $student->id }}" value="{{ $s }}"
                                      {{ ($att && $att->presence_status === $s) ? 'checked' : '' }}> {{ $s }}
                                  </label>
                                @endforeach
                              </div>
                              <div class="mb-2 d-none" id="ijin-options-{{ $student->id }}">
                                <strong>Izin melalui:</strong><br>
                                @foreach(['Surat','Telepon','Datang ke Sekolah','Lainnya'] as $opt)
                                  <label class="me-2"><input type="radio" name="ijin-{{ $student->id }}" value="{{ $opt }}"> {{ $opt }}</label>
                                @endforeach
                              </div>
                              {{-- Upload bukti — tampil saat Ijin atau Sakit --}}
                              <div class="mb-2 d-none" id="evidence-upload-{{ $student->id }}">
                                <strong>Upload Bukti Surat <span class="text-muted small">(opsional, jpg/png/pdf maks 2MB)</span>:</strong><br>
                                <input type="file" id="evidence-{{ $student->id }}" class="form-control form-control-sm mt-1"
                                  accept=".jpg,.jpeg,.png,.pdf">
                                @if($att && $att->evidence)
                                  <div class="mt-1 text-muted small">
                                    Bukti sebelumnya:
                                    @php $ext = strtolower(pathinfo($att->evidence, PATHINFO_EXTENSION)); @endphp
                                    @if($ext === 'pdf')
                                      <a href="{{ asset('storage/' . $att->evidence) }}" target="_blank">Lihat PDF</a>
                                    @else
                                      <img src="{{ asset('storage/' . $att->evidence) }}" style="max-height:40px;border-radius:3px;">
                                    @endif
                                  </div>
                                @endif
                              </div>
                              <div class="mb-2">
                                <strong>Keterangan (opsional):</strong><br>
                                <input type="text" id="ket-{{ $student->id }}" class="form-control"
                                  placeholder="Contoh: ada surat dari orang tua"
                                  value="{{ $att->description ?? '' }}">
                              </div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center align-items-end">
                              <button type="button" class="btn btn-primary btn-save-sim" data-student-id="{{ $student->id }}">SIMPAN DATA</button>
                              <button class="btn btn-outline-secondary btn-sm mt-2 btn-close-expand" data-target="#expand-d-{{ $student->id }}">Tutup</button>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endif
                  @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada data absensi</td></tr>
                  @endforelse
                </tbody>
              </table>

              {{-- ===== MONTHLY ===== --}}
              @elseif ($view == 'monthly')
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th style="width:60px">No</th>
                    <th>Nama Siswa</th>
                    <th>Alpa</th>
                    <th>Ijin</th>
                    <th>Sakit</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($students as $student)
                    @php
                      $studentAttendances = $attendances->get($student->id) ?? collect([]);
                      $alpa  = $studentAttendances->where('presence_status','Alpa')->count();
                      $ijin  = $studentAttendances->where('presence_status','Ijin')->count();
                      $sakit = $studentAttendances->where('presence_status','Sakit')->count();
                    @endphp
                    <tr class="student-row expand-toggle" data-student-id="{{ $student->id }}" style="cursor:pointer;">
                      <td>{{ $loop->iteration }}</td>
                      <td class="text-primary">{{ $student->name }}</td>
                      <td>{{ $alpa }}</td>
                      <td>{{ $ijin }}</td>
                      <td>{{ $sakit }}</td>
                    </tr>
                    <tr class="expand-row d-none" id="expand-m-{{ $student->id }}">
                      <td colspan="5" class="hiddenRow">
                        <div class="expand-box">
                          <h6 class="mb-2">Detail Absensi Bulan {{ \Carbon\Carbon::create()->month(intval($selectedMonth))->translatedFormat('F') }} — {{ $student->name }}</h6>
                          <table class="table table-sm">
                            <thead><tr><th>Tanggal</th><th>Status</th><th>Keterangan</th><th>Bukti</th></tr></thead>
                            <tbody>
                              @php
                                $detailAbsensi = $attendances->get($student->id, collect([]))
                                  ->filter(fn($att) => \Carbon\Carbon::parse($att->date)->month == intval($selectedMonth));
                              @endphp
                              @forelse($detailAbsensi as $det)
                                <tr>
                                  <td>{{ \Carbon\Carbon::parse($det->date)->translatedFormat('d F Y') }}</td>
                                  <td>{{ $det->presence_status }}</td>
                                  <td>{{ $det->note ?? $det->description ?? '-' }}</td>
                                  <td>
                                    @if($det->evidence)
                                      @php $ext = strtolower(pathinfo($det->evidence, PATHINFO_EXTENSION)); @endphp
                                      @if($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $det->evidence) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0">PDF</a>
                                      @else
                                        <img src="{{ asset('storage/' . $det->evidence) }}"
                                          style="max-width:50px;max-height:50px;object-fit:cover;border-radius:3px;cursor:pointer;"
                                          data-bs-toggle="modal" data-bs-target="#evidenceModal"
                                          data-src="{{ asset('storage/' . $det->evidence) }}">
                                      @endif
                                    @else
                                      -
                                    @endif
                                  </td>
                                </tr>
                              @empty
                                <tr><td colspan="4" class="text-center">Belum ada data absensi bulan ini</td></tr>
                              @endforelse
                            </tbody>
                          </table>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              {{-- ===== SEMESTER ===== --}}
              @elseif ($view == 'semester')
              @php
                $semester      = request()->input('semester','ganjil');
                $bulanSemester = $semester === 'ganjil' ? [7,8,9,10,11,12] : [1,2,3,4,5,6];
              @endphp
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    @foreach ($bulanSemester as $b)
                      <th>{{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}</th>
                    @endforeach
                    <th colspan="3" class="text-center">Jml Absen</th>
                  </tr>
                  <tr>
                    <th></th><th></th>
                    @foreach ($bulanSemester as $b)<th></th>@endforeach
                    <th class="text-center">S</th>
                    <th class="text-center">I</th>
                    <th class="text-center">A</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($students as $student)
                    @php $totalSakit=0; $totalIjin=0; $totalAlpa=0; @endphp
                    <tr class="student-row expand-toggle" data-student-id="{{ $student->id }}" style="cursor:pointer;">
                      <td>{{ $loop->iteration }}</td>
                      <td class="text-primary">{{ $student->name }}</td>
                      @foreach ($bulanSemester as $b)
                        @php
                          $s = $semesterBreakdown[$student->id][$b]['Sakit'] ?? 0;
                          $i = $semesterBreakdown[$student->id][$b]['Ijin']  ?? 0;
                          $a = $semesterBreakdown[$student->id][$b]['Alpa']  ?? 0;
                          $totalSakit += $s; $totalIjin += $i; $totalAlpa += $a;
                        @endphp
                        <td>S:{{ $s }} I:{{ $i }} A:{{ $a }}</td>
                      @endforeach
                      <td class="text-center">{{ $totalSakit }}</td>
                      <td class="text-center">{{ $totalIjin }}</td>
                      <td class="text-center">{{ $totalAlpa }}</td>
                    </tr>
                    <tr class="expand-row d-none" id="expand-s-{{ $student->id }}">
                      <td colspan="{{ 4 + count($bulanSemester) }}" class="hiddenRow">
                        <div class="expand-box">
                          <h6 class="mb-2">Detail Semester {{ ucfirst($semester) }} — {{ $student->name }}</h6>
                          <table class="table table-sm">
                            <thead><tr><th>Tanggal</th><th>Status</th><th>Keterangan</th><th>Bukti</th></tr></thead>
                            <tbody>
                              @php
                                $detailAbsensi = $attendances->filter(fn($att) =>
                                  $att->student_id == $student->id &&
                                  in_array(\Carbon\Carbon::parse($att->date)->month, $bulanSemester)
                                );
                              @endphp
                              @forelse($detailAbsensi as $det)
                                <tr>
                                  <td>{{ \Carbon\Carbon::parse($det->date)->translatedFormat('d F Y') }}</td>
                                  <td>{{ $det->presence_status }}</td>
                                  <td>{{ $det->note ?? $det->description ?? '-' }}</td>
                                  <td>
                                    @if($det->evidence)
                                      @php $ext = strtolower(pathinfo($det->evidence, PATHINFO_EXTENSION)); @endphp
                                      @if($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $det->evidence) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0">PDF</a>
                                      @else
                                        <img src="{{ asset('storage/' . $det->evidence) }}"
                                          style="max-width:50px;max-height:50px;object-fit:cover;border-radius:3px;cursor:pointer;"
                                          data-bs-toggle="modal" data-bs-target="#evidenceModal"
                                          data-src="{{ asset('storage/' . $det->evidence) }}">
                                      @endif
                                    @else
                                      -
                                    @endif
                                  </td>
                                </tr>
                              @empty
                                <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                              @endforelse
                            </tbody>
                          </table>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              @endif

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Preview Bukti --}}
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bukti Surat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="evidenceModalImg" src="" style="max-width:100%;border-radius:8px;">
      </div>
      <div class="modal-footer justify-content-center">
        <a id="evidenceDownloadBtn" href="#" download
          class="btn btn-primary">
          <i class="uil uil-download-alt"></i> Unduh Bukti
        </a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    ['selectedDate','selectedMonth','selectedYear'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => el.closest('form').submit());
    });

    // Expand/collapse rows
    document.querySelectorAll('.expand-toggle').forEach(el => {
        el.addEventListener('click', function () {
            const tr = this.closest('tr.student-row') || this.closest('tr');
            if (!tr) return;
            const studentId = tr.dataset.studentId;

            document.querySelectorAll('.expand-row').forEach(r => {
                if (!r.id.endsWith('-' + studentId)) r.classList.add('d-none');
            });

            ['expand-d-','expand-m-','expand-s-'].forEach(prefix => {
                const row = document.getElementById(prefix + studentId);
                if (row) {
                    row.classList.toggle('d-none');
                    if (prefix === 'expand-d-' && !row.classList.contains('d-none')) {
                        attachIjinListeners(studentId);
                    }
                }
            });
        });
    });

    document.querySelectorAll('.btn-close-expand').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const el = document.querySelector(this.dataset.target);
            if (el) el.classList.add('d-none');
        });
    });

    // Simpan absensi via AJAX (FormData untuk support file upload)
    document.querySelectorAll('.btn-save-sim').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.dataset.studentId;
            const status    = document.querySelector(`input[name="status-${studentId}"]:checked`)?.value;
            const ijin      = document.querySelector(`input[name="ijin-${studentId}"]:checked`)?.value || '';
            const ket       = document.getElementById('ket-' + studentId)?.value || '';
            const fileInput = document.getElementById('evidence-' + studentId);

            if (!status) { alert('Silakan pilih status kehadiran dulu!'); return; }

            const dateInput = document.querySelector('input[name="date"]');
            const tanggal   = dateInput ? dateInput.value : "{{ request('date') ?? now()->format('Y-m-d') }}";

            const formData = new FormData();
            formData.append('student_id', studentId);
            formData.append('presence_status', status);
            formData.append('izin_via', ijin);
            formData.append('keterangan', ket);
            formData.append('date', tanggal);
            formData.append('_token', '{{ csrf_token() }}');
            if (fileInput && fileInput.files[0]) {
                formData.append('evidence', fileInput.files[0]);
            }

            fetch("{{ route('attendance.store') }}", {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`.student-row[data-student-id="${studentId}"] .status-cell`).textContent = status;
                    const expand = document.getElementById('expand-d-' + studentId);
                    if (expand) expand.classList.add('d-none');
                    alert('✅ Data berhasil disimpan!');
                    location.reload(); // reload untuk tampilkan bukti terbaru
                } else {
                    alert('❌ Gagal menyimpan data.');
                }
            })
            .catch(err => console.error(err));
        });
    });

    // Modal preview gambar
    document.querySelectorAll('[data-bs-target="#evidenceModal"]').forEach(img => {
    img.addEventListener('click', function () {
        const src = this.dataset.src;
        document.getElementById('evidenceModalImg').src = src;
        document.getElementById('evidenceDownloadBtn').href = src;
        });
    });
});

function attachIjinListeners(studentId) {
    document.querySelectorAll(`input[name="status-${studentId}"]`).forEach(r => {
        r.addEventListener('change', function () {
            const opsiIjin     = document.getElementById('ijin-options-' + studentId);
            const opsiEvidence = document.getElementById('evidence-upload-' + studentId);

            if (this.value === 'Ijin') {
                opsiIjin?.classList.remove('d-none');
                opsiEvidence?.classList.remove('d-none');
            } else if (this.value === 'Sakit') {
                opsiIjin?.classList.add('d-none');
                opsiEvidence?.classList.remove('d-none');
                document.querySelectorAll(`input[name="ijin-${studentId}"]`).forEach(s => s.checked = false);
            } else {
                opsiIjin?.classList.add('d-none');
                opsiEvidence?.classList.add('d-none');
                document.querySelectorAll(`input[name="ijin-${studentId}"]`).forEach(s => s.checked = false);
            }
        });

        // Trigger untuk status yang sudah terisi
        if (r.checked) r.dispatchEvent(new Event('change'));
    });
}
</script>
@endpush
@endsection
