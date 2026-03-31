@extends('layouts.dashboard')
@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="m-0 text-primary">Asesmen Siswa</h5>

              {{-- Tombol aksi sesuai tab aktif --}}
              <div id="actions-tab1" class="d-flex gap-2">
                @if(in_array($role, ['Admin', 'Guru BK']))
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLinkModal">+ Tambah Instrumen</button>
                @endif
              </div>
              <div id="actions-tab2" class="d-flex gap-2" style="display:none!important;">
                @can('Tambah Asesmen Siswa')
                {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentAssessmentModal">Tambah</button> --}}
                <a href="{{ route('student_assessment.export') }}" class="btn btn-success btn-sm">Ekspor</a>
                @endcan
              </div>
            </div>

            {{-- TABS --}}
            <div style="padding: 0 20px; border-bottom: 0.5px solid #e2e8f0; display:flex; gap:0;">
              <div id="tab1-btn" onclick="switchTab(1)"
                style="padding:12px 20px;font-size:13px;font-weight:500;color:#185FA5;border-bottom:2px solid #185FA5;cursor:pointer;">
                Instrumen Asesmen
              </div>
              <div id="tab2-btn" onclick="switchTab(2)"
                style="padding:12px 20px;font-size:13px;color:#64748b;border-bottom:2px solid transparent;cursor:pointer;">
                Hasil Asesmen Siswa
              </div>
            </div>

            {{-- ===== TAB 1: INSTRUMEN ASESMEN ===== --}}
            <div id="panel-tab1" style="padding:20px;">
              @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                  {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              @endif

              @if($links->isEmpty())
                <div class="text-center py-5 text-muted">
                  <i class="uil uil-clipboard-notes" style="font-size:48px;opacity:0.3;"></i>
                  <p class="mt-2">Belum ada instrumen asesmen yang ditambahkan.</p>
                </div>
              @else
              <div class="row g-3">
                @foreach($links as $link)
                <div class="col-xl-3 col-md-4 col-sm-6">
                  <div class="card h-100 border" style="border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                      <div style="width:72px;height:72px;border-radius:16px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:16px;overflow:hidden;">
                        @if($link->icon)
                          <img src="{{ asset('storage/' . $link->icon) }}" style="width:100%;height:100%;object-fit:contain;">
                        @else
                          <i class="uil uil-clipboard-notes" style="font-size:36px;color:#2563eb;"></i>
                        @endif
                      </div>
                      <h6 class="fw-bold mb-1" style="font-size:14px;">{{ $link->name }}</h6>
                      @if($link->description)
                        <p class="text-muted mb-3" style="font-size:12px;line-height:1.5;">{{ Str::limit($link->description, 80) }}</p>
                      @else
                        <p class="text-muted mb-3" style="font-size:12px;">-</p>
                      @endif
                      <a href="{{ $link->url }}" target="_blank" class="btn btn-primary btn-sm w-100 mt-auto">
                        <i class="uil uil-external-link-alt me-1"></i> Buka Asesmen
                      </a>
                      @if(in_array($role, ['Admin', 'Guru BK']))
                      <div class="d-flex gap-2 mt-2 w-100">
                        <button class="btn btn-warning btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editLinkModal{{ $link->id }}">Edit</button>
                        <button class="btn btn-danger btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#deleteLinkModal{{ $link->id }}">Hapus</button>
                      </div>
                      @endif
                    </div>
                    <div class="card-footer text-muted" style="font-size:11px;border-top:1px solid #f0f0f0;">
                      Ditambah oleh {{ optional($link->user)->name ?? '-' }}
                    </div>
                  </div>
                </div>

                {{-- Edit Link Modal --}}
                @if(in_array($role, ['Admin', 'Guru BK']))
                <div class="modal fade" id="editLinkModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit: {{ $link->name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('assessment_link.update', $link->id) }}" method="POST" enctype="multipart/form-data">
                      @csrf @method('PUT')
                      <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nama Asesmen</label><input type="text" class="form-control" name="name" value="{{ $link->name }}" required></div>
                        <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3">{{ $link->description }}</textarea></div>
                        <div class="mb-3">
                          <label class="form-label">Icon / Logo</label>
                          @if($link->icon)
                            <div class="mb-2"><img src="{{ asset('storage/' . $link->icon) }}" style="height:48px;border-radius:8px;"><small class="text-muted ms-2">Biarkan kosong jika tidak ingin mengganti.</small></div>
                          @endif
                          <input type="file" class="form-control" name="icon" accept=".jpg,.jpeg,.png,.svg">
                        </div>
                        <div class="mb-3"><label class="form-label">Link URL</label><input type="url" class="form-control" name="url" value="{{ $link->url }}" required></div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                    </form>
                  </div></div>
                </div>
                {{-- Delete Link Modal --}}
                <div class="modal fade" id="deleteLinkModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Hapus Instrumen</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Yakin ingin menghapus <strong>{{ $link->name }}</strong>?</div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <form action="{{ route('assessment_link.destroy', $link->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                      </form>
                    </div>
                  </div></div>
                </div>
                @endif
                @endforeach
              </div>
              @endif
            </div>

            {{-- ===== TAB 2: HASIL ASESMEN SISWA ===== --}}
            <div id="panel-tab2" style="display:none;">
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
                              <th>Kelas</th>
                              <th>Nama Siswa</th>
                              @foreach ($assessments as $assessment)
                                <th>{{ $assessment->id }}</th>
                              @endforeach
                              <th>Total</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            @php
                              $totalScores = array_fill(0, count($assessments), 0);
                              $overallTotalScore = 0;
                              $fieldTotals = [];
                            @endphp
                            @foreach ($students as $student)
                              @if ($student->student_assessments()->whereNotNull('answer')->exists())
                                <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $student->class->class_level . ' ' . $student->class->major->major_name . ' ' . $student->class->classroom }}</td>
                                  <td>{{ $student->name }}</td>
                                  @php $studentTotalScore = 0; @endphp
                                  @foreach ($assessments as $key => $assessment)
                                    @php
                                      $sa = $student->student_assessments()->where('assessment_id', $assessment->id)->first();
                                      $answer = $sa ? $sa->answer : null;
                                      if ($answer === 1) {
                                        $studentTotalScore++;
                                        $totalScores[$key]++;
                                        $fieldTotals[$assessment->field] = ($fieldTotals[$assessment->field] ?? 0) + 1;
                                      }
                                    @endphp
                                    <td style="background-color:@switch($assessment->field)@case('Pribadi')#d4edda;@break @case('Sosial')#fff3cd;@break @case('Belajar')#ffeeba;@break @case('Karir')#f8d7da;@break @default transparent;@endswitch">
                                      {{ $answer === 1 ? '1' : ($answer === 0 ? '0' : '-') }}
                                    </td>
                                  @endforeach
                                  <td>{{ $studentTotalScore }}</td>
                                  <td>
                                    @can('Ubah Asesmen Siswa')
                                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit_data{{ $student->id }}">Edit</a>
                                    @endcan
                                    @can('Hapus Asesmen Siswa')
                                    <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_data{{ $student->id }}">Hapus</a>
                                    @endcan
                                  </td>

                                  {{-- Edit Modal --}}
                                  <div class="modal fade" id="edit_data{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog"><div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Asesmen: {{ $student->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <form action="{{ route('student_assessment.update', $student->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                          <div class="mb-3">
                                            <label class="col-form-label">Nama Siswa</label>
                                            <select class="form-control" name="student_id" required>
                                              @foreach($students as $std)
                                                <option value="{{ $std->id }}" {{ $student->id==$std->id?'selected':'' }}>{{ $std->name }}</option>
                                              @endforeach
                                            </select>
                                          </div>
                                          @foreach($assessments as $assessment)
                                            @php $sa = $student->student_assessments()->where('assessment_id',$assessment->id)->first(); $answer=$sa?$sa->answer:null; @endphp
                                            <div class="mb-3">
                                              <label class="col-form-label">{{ $assessment->question }}</label>
                                              <div class="d-flex">
                                                <input type="radio" name="answers[{{ $assessment->id }}]" value="1" {{ $answer===1?'checked':'' }} required>
                                                <label class="ms-2">Iya</label>
                                                <div class="ms-3"></div>
                                                <input type="radio" name="answers[{{ $assessment->id }}]" value="0" {{ $answer===0?'checked':'' }} required>
                                                <label class="ms-2">Tidak</label>
                                              </div>
                                            </div>
                                          @endforeach
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
                                        <h5 class="modal-title">Hapus Asesmen: {{ $student->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">Yakin ingin menghapus semua asesmen <strong>{{ $student->name }}</strong>?</div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('student_assessment.destroy', $student->id) }}" method="POST">
                                          @csrf @method('DELETE')
                                          <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                      </div>
                                    </div></div>
                                  </div>
                                </tr>
                                @php $overallTotalScore += $studentTotalScore; @endphp
                              @endif
                            @endforeach
                          </tbody>
                          <tfoot style="background-color:#007bff;color:white;">
                            <tr>
                              <th colspan="3" style="text-align:right;">Jumlah Konseli</th>
                              @foreach($assessments as $key => $assessment)<th>{{ $totalScores[$key] }}</th>@endforeach
                              <th>{{ $overallTotalScore }}</th><th></th>
                            </tr>
                            <tr>
                              <th colspan="3" style="text-align:right;">Persentase Butir</th>
                              @foreach($totalScores as $score)
                                <th>{{ $overallTotalScore > 0 ? number_format(($score/$overallTotalScore)*100,2).'%' : '0%' }}</th>
                              @endforeach
                              <th>100%</th><th></th>
                            </tr>
                            <tr>
                              <th colspan="3" style="text-align:right;">Jumlah per Bidang</th>
                              @php $fieldTotal = array_sum($fieldTotals); @endphp
                              @foreach(['Pribadi','Sosial','Belajar','Karir'] as $field)
                                @php $fieldCount = count(array_filter($assessments->toArray(), fn($a) => $a['field']===$field)); @endphp
                                <th colspan="{{ $fieldCount }}" style="text-align:center;">{{ $fieldTotals[$field] ?? 0 }}</th>
                              @endforeach
                              <th>{{ $fieldTotal }}</th><th></th>
                            </tr>
                            <tr>
                              <th colspan="3" style="text-align:right;">Persentase per Bidang</th>
                              @foreach(['Pribadi','Sosial','Belajar','Karir'] as $field)
                                @php $fieldCount=count(array_filter($assessments->toArray(),fn($a)=>$a['field']===$field)); $pct=$fieldTotal>0?(($fieldTotals[$field]??0)/$fieldTotal*100):0; @endphp
                                <th colspan="{{ $fieldCount }}" style="text-align:center;">{{ number_format($pct,2) }}%</th>
                              @endforeach
                              <th>100%</th><th></th>
                            </tr>
                          </tfoot>
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
  </div>

  {{-- Modal Tambah Instrumen --}}
  @if(in_array($role, ['Admin', 'Guru BK']))
  <div class="modal fade" id="addLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Instrumen Asesmen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('assessment_link.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama Asesmen <span class="text-danger">*</span></label><input type="text" class="form-control" name="name" required placeholder="Contoh: DCM (Daftar Cek Masalah)"></div>
          <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3" placeholder="Deskripsi singkat..."></textarea></div>
          <div class="mb-3"><label class="form-label">Icon / Logo <span class="text-muted small">(jpg, png, svg)</span></label><input type="file" class="form-control" name="icon" accept=".jpg,.jpeg,.png,.svg"></div>
          <div class="mb-3"><label class="form-label">Link URL <span class="text-danger">*</span></label><input type="url" class="form-control" name="url" required placeholder="https://..."><small class="text-muted">Link website asesmen resmi</small></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div></div>
  </div>
  @endif

  {{-- Modal Tambah Asesmen Siswa --}}
  @can('Tambah Asesmen Siswa')
  <div class="modal fade" id="addStudentAssessmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Asesmen Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('student_assessment.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="col-form-label">Nama Siswa</label>
            <select class="form-control" name="student_id" required>
              <option value="">-- Pilih Nama Siswa --</option>
              @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
              @endforeach
            </select>
          </div>
          @foreach($assessments as $assessment)
            <div class="mb-3">
              <label class="col-form-label">{{ $assessment->question }}</label>
              <div class="d-flex">
                <input type="radio" name="answers[{{ $assessment->id }}]" value="1" required>
                <label class="ms-2">Iya</label>
                <div class="ms-3"></div>
                <input type="radio" name="answers[{{ $assessment->id }}]" value="0" required>
                <label class="ms-2">Tidak</label>
              </div>
            </div>
          @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div></div>
  </div>
  @endcan

@push('scripts')
<script>
function switchTab(n) {
  document.getElementById('tab1-btn').style.color      = n===1 ? '#185FA5' : '#64748b';
  document.getElementById('tab1-btn').style.borderBottom = n===1 ? '2px solid #185FA5' : '2px solid transparent';
  document.getElementById('tab2-btn').style.color      = n===2 ? '#185FA5' : '#64748b';
  document.getElementById('tab2-btn').style.borderBottom = n===2 ? '2px solid #185FA5' : '2px solid transparent';
  document.getElementById('panel-tab1').style.display  = n===1 ? 'block' : 'none';
  document.getElementById('panel-tab2').style.display  = n===2 ? 'block' : 'none';
  document.getElementById('actions-tab1').style.display = n===1 ? 'flex' : 'none';
  document.getElementById('actions-tab2').style.display = n===2 ? 'flex' : 'none';
}
</script>
@endpush
@endsection
