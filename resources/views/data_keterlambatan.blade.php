@extends('layouts.dashboard')
@section('content')

<div class="content">
  <div class="row pt-4">
    <div class="mb-4">
      <div class="card shadow mb-4">

        {{-- HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="m-0 text-primary">Rekap Keterlambatan Siswa</h5>

          <form method="GET" action="{{ route('lateness.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
            @if(!$isSiswa && !$isWaliKelas)
            <select name="class" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Semua Kelas</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                  {{ $class->class_level }} {{ $class->major->major_name }} {{ $class->classroom }}
                </option>
              @endforeach
            </select>
            @endif
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
          </form>

          @if($isGuruBK)
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLatenessModal">
            + Tambah Keterlambatan
          </button>
          @endif
        </div>

        {{-- TABEL --}}
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Nama Siswa</th>
                  <th>Kelas</th>
                  <th>Tanggal</th>
                  <th>Jam Masuk</th>
                  <th>Alasan</th>
                  <th>Bukti</th>
                  @if($isGuruBK)
                  <th>Aksi</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @forelse($latenesses as $i => $late)
                <tr>
                  <td>{{ $i+1 }}</td>
                  <td>{{ $late->student->name }}</td>
                  <td>{{ $late->student->class->class_level ?? '-' }} {{ $late->student->class->major->major_name ?? '' }} {{ $late->student->class->classroom ?? '' }}</td>
                  <td>{{ \Carbon\Carbon::parse($late->date)->translatedFormat('d F Y') }}</td>
                  <td>{{ \Carbon\Carbon::parse($late->time_in)->format('H:i') }}</td>
                  <td>{{ $late->reason ?? '-' }}</td>
                  <td>
                    @if($late->evidence)
                      <img src="{{ asset('storage/' . $late->evidence) }}"
                        style="max-width:50px;max-height:50px;object-fit:cover;border-radius:4px;cursor:pointer;"
                        data-bs-toggle="modal" data-bs-target="#evidenceModal"
                        data-src="{{ asset('storage/' . $late->evidence) }}">
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  @if($isGuruBK)
                  <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                      data-bs-target="#editLatenessModal"
                      data-id="{{ $late->id }}"
                      data-student="{{ $late->student_id }}"
                      data-date="{{ $late->date }}"
                      data-time="{{ $late->time_in }}"
                      data-reason="{{ $late->reason }}">
                      Edit
                    </button>
                    <form action="{{ route('lateness.destroy', $late->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus data ini?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                  </td>
                  @endif
                </tr>
                @empty
                <tr><td colspan="{{ $isGuruBK ? 8 : 7 }}" class="text-center text-muted py-3">Belum ada data keterlambatan</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Tambah --}}
@if($isGuruBK)
<div class="modal fade" id="addLatenessModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Keterlambatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('lateness.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Siswa</label>
            <select name="student_id" class="form-select" required>
              <option value="">Pilih Siswa</option>
              @foreach(\App\Models\Student::with('class.major')->orderBy('name')->get() as $student)
                <option value="{{ $student->id }}">
                  {{ $student->name }} — {{ $student->class->class_level ?? '' }} {{ $student->class->major->major_name ?? '' }} {{ $student->class->classroom ?? '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jam Masuk</label>
            <input type="time" name="time_in" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alasan</label>
            <input type="text" name="reason" class="form-control" placeholder="Contoh: ban bocor, macet...">
          </div>
          <div class="mb-3">
            <label class="form-label">Bukti Foto <span class="text-muted small">(opsional, maks 2MB)</span></label>
            <input type="file" name="evidence" class="form-control" accept=".jpg,.jpeg,.png">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editLatenessModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Keterlambatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="" id="editLatenessForm" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Siswa</label>
            <select name="student_id" id="edit_student_id" class="form-select" required>
              <option value="">Pilih Siswa</option>
              @foreach(\App\Models\Student::with('class.major')->orderBy('name')->get() as $student)
                <option value="{{ $student->id }}">
                  {{ $student->name }} — {{ $student->class->class_level ?? '' }} {{ $student->class->major->major_name ?? '' }} {{ $student->class->classroom ?? '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" id="edit_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jam Masuk</label>
            <input type="time" name="time_in" id="edit_time_in" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alasan</label>
            <input type="text" name="reason" id="edit_reason" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Bukti Foto Baru <span class="text-muted small">(opsional)</span></label>
            <input type="file" name="evidence" class="form-control" accept=".jpg,.jpeg,.png">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- Modal Preview Bukti --}}
<div class="modal fade" id="evidenceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bukti Keterlambatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="evidenceModalImg" src="" style="max-width:100%;border-radius:8px;">
      </div>
      <div class="modal-footer justify-content-center">
        <a id="evidenceDownloadBtn" href="#" download class="btn btn-primary">
          <i class="uil uil-download-alt"></i> Unduh Bukti
        </a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview bukti
    document.querySelectorAll('[data-bs-target="#evidenceModal"]').forEach(img => {
        img.addEventListener('click', function () {
            const src = this.dataset.src;
            document.getElementById('evidenceModalImg').src = src;
            document.getElementById('evidenceDownloadBtn').href = src;
        });
    });

    // Edit modal
    const editModal = document.getElementById('editLatenessModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('edit_student_id').value = btn.dataset.student;
            document.getElementById('edit_date').value       = btn.dataset.date;
            document.getElementById('edit_time_in').value    = btn.dataset.time;
            document.getElementById('edit_reason').value     = btn.dataset.reason;
            document.getElementById('editLatenessForm').action = '/keterlambatan/' + btn.dataset.id;
        });
    }
});
</script>
@endpush

@endsection