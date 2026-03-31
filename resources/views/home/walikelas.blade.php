@extends('layouts.dashboard')

@section('content')

{{-- BANNER --}}
<div class="dash-banner anim-1" style="background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 55%, #a78bfa 100%); margin-bottom:24px;">
  <div>
    <h2><span>Selamat Pagi,</span><br>{{ auth()->user()->name }}</h2>
    <p>Pantau perkembangan siswa Anda hari ini.</p>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
      <div class="banner-badge">👨‍🏫 WALI KELAS</div>
      @if($myClass)
      <div class="banner-badge" style="background:rgba(255,255,255,0.28);">📚 {{ $myClass->class_level }} {{ $myClass->major->major_name ?? '' }} {{ $myClass->classroom }}</div>
      @endif
    </div>
  </div>
  {{-- INFO KELAS --}}
  @if($myClass)
  <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:16px 20px;flex-shrink:0;min-width:210px;border:1px solid rgba(255,255,255,0.2);align-self:center;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">📚</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Kelas Diampu</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">
          {{ $myClass->class_level }} {{ optional($myClass->major)->major_name }} {{ $myClass->classroom }}
        </div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">🏫</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Jurusan</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">{{ optional($myClass->major)->major_name ?? '-' }}</div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">👨‍🎓</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Jumlah Siswa</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">{{ $total_students_in_class }} siswa</div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
      <span style="font-size:14px;width:20px;text-align:center;">✅</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Status</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">Aktif</div>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4 anim-2">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f5f3ff;color:#7c3aed"><i class="uil uil-users-alt"></i></div>
      <div class="stat-label">Siswa di Kelas</div>
      <div class="stat-value">{{ $total_students_in_class }}</div>
      <div class="stat-sub">{{ $myClass ? $myClass->class_level.' '.(optional($myClass->major)->major_name ?? '').' '.$myClass->classroom : '-' }}</div>
      <div class="stat-accent" style="background:#7c3aed"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff7ed;color:#ea580c"><i class="uil uil-exclamation-triangle"></i></div>
      <div class="stat-label">Kasus di Kelas</div>
      <div class="stat-value">{{ $cases_in_class }}</div>
      <div class="stat-sub">Perlu perhatian</div>
      <div class="stat-accent" style="background:#ea580c"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#ecfeff;color:#0891b2"><i class="uil uil-clipboard-notes"></i></div>
      <div class="stat-label">Bimbingan Kelas</div>
      <div class="stat-value">{{ $guidances_in_class }}</div>
      <div class="stat-sub">Total sesi</div>
      <div class="stat-accent" style="background:#0891b2"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fef9c3;color:#ca8a04"><i class="uil uil-trophy"></i></div>
      <div class="stat-label">Prestasi Kelas</div>
      <div class="stat-value">{{ $achievements_in_class }}</div>
      <div class="stat-sub">Tahun ini</div>
      <div class="stat-accent" style="background:#ca8a04"></div>
    </div>
  </div>
</div>

{{-- 3 PANEL --}}
<div class="row g-3 mb-4 anim-3">

  {{-- PERLU PERHATIAN --}}
  <div class="col-lg-4">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <h3>⚠️ Perlu Perhatian</h3>
        @can('Lihat Kasus')<a href="{{ route('case.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($students_with_cases as $student)
      <div style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border);">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random&size=34" style="width:34px;height:34px;border-radius:8px;object-fit:cover;flex-shrink:0;">
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $student->name }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $student->cases_count }} kasus aktif</div>
        </div>
        @if($student->cases_count >= 3)
          <span style="font-size:10px;font-weight:700;background:#fef2f2;color:#991b1b;padding:3px 8px;border-radius:20px;">Perlu Tindak</span>
        @else
          <span style="font-size:10px;font-weight:700;background:#fef9c3;color:#854d0e;padding:3px 8px;border-radius:20px;">Pantau</span>
        @endif
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-check-circle" style="font-size:2rem;color:#16a34a;display:block;margin-bottom:8px;"></i>
        Tidak ada siswa bermasalah
      </div>
      @endforelse
    </div>
  </div>

  {{-- BIMBINGAN SISWA --}}
  <div class="col-lg-4">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <h3>📝 Bimbingan Siswa</h3>
        @can('Lihat Bimbingan')<a href="{{ route('guidance.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($recent_guidances as $guidance)
      <div style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border);">
        <div style="width:10px;height:10px;border-radius:50%;background:#7c3aed;flex-shrink:0;"></div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $guidance->student->name ?? 'Siswa' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $guidance->topic ?? 'Bimbingan' }}</div>
        </div>
        <div style="font-size:11px;color:var(--muted);">{{ \Carbon\Carbon::parse($guidance->date)->format('d M') }}</div>
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-notes" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Belum ada bimbingan
      </div>
      @endforelse
    </div>
  </div>

  {{-- PRESTASI SISWA --}}
  <div class="col-lg-4">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <h3>🏆 Prestasi Siswa</h3>
        @can('Lihat Prestasi')<a href="{{ route('achievement.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($recent_achievements as $achievement)
      <div style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border);">
        <div style="width:34px;height:34px;border-radius:8px;background:#fefce8;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🏅</div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $achievement->student->name ?? 'Siswa' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ Str::limit($achievement->name ?? 'Prestasi', 30) }}</div>
        </div>
        <div style="font-size:11px;color:var(--muted);">{{ \Carbon\Carbon::parse($achievement->date)->format('d M') }}</div>
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-trophy" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Belum ada prestasi
      </div>
      @endforelse
    </div>
  </div>

</div>

{{-- TABEL DAFTAR SISWA --}}
@if($myClass && $students_in_class->count() > 0)
<div class="dash-card anim-4">
  <div class="dash-card-header">
    <h3>👥 Daftar Siswa — {{ $myClass->class_level }} {{ $myClass->major->major_name ?? '' }} {{ $myClass->classroom }}</h3>
    <span style="font-size:12px;color:var(--muted);">{{ $total_students_in_class }} siswa</span>
  </div>
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead>
        <tr style="background:#f8fafc;">
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">#</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">Nama Siswa</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">NIS</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">Kasus</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">Bimbingan</th>
          <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--border);">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students_in_class as $i => $student)
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:12px 16px;color:var(--muted);">{{ $i + 1 }}</td>
          <td style="padding:12px 16px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random&size=30" style="width:30px;height:30px;border-radius:8px;object-fit:cover;">
              <span style="font-weight:600;">{{ $student->name }}</span>
            </div>
          </td>
          <td style="padding:12px 16px;color:var(--muted);">{{ $student->nis ?? '-' }}</td>
          <td style="padding:12px 16px;">
            @php $caseCount = $students_with_cases->firstWhere('id', $student->id)->cases_count ?? 0; @endphp
            {{ $caseCount }}
          </td>
          <td style="padding:12px 16px;">{{ $student->guidances_count ?? 0 }}</td>
          <td style="padding:12px 16px;">
            @if(($students_with_cases->firstWhere('id', $student->id)->cases_count ?? 0) >= 3)
              <span style="font-size:11px;font-weight:600;background:#fef2f2;color:#991b1b;padding:3px 10px;border-radius:20px;">Bermasalah</span>
            @elseif(($students_with_cases->firstWhere('id', $student->id)->cases_count ?? 0) > 0)
              <span style="font-size:11px;font-weight:600;background:#fef9c3;color:#854d0e;padding:3px 10px;border-radius:20px;">Dipantau</span>
            @else
              <span style="font-size:11px;font-weight:600;background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:20px;">Baik</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection
