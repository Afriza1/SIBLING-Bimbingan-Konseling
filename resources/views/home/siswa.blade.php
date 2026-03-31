@extends('layouts.dashboard')

@section('content')

{{-- BANNER --}}
<div class="dash-banner anim-1" style="background: linear-gradient(135deg, #0369a1 0%, #0891b2 50%, #06b6d4 100%); margin-bottom:24px; align-items:stretch; gap:20px;">
  <div style="flex:1;">
    <h2><span>Halo,</span><br>{{ auth()->user()->name }} 👋</h2>
    <p>Semangat belajar hari ini! Pantau perkembanganmu.</p>
    <div class="banner-badge">🎓 SISWA</div>
  </div>
  {{-- INFO DIRI --}}
  @if($student)
  <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:16px 20px;flex-shrink:0;min-width:210px;border:1px solid rgba(255,255,255,0.2);align-self:center;">
    {{-- <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">🪪</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">NIS</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">{{ $student->nis ?? '-' }}</div>
      </div>
    </div> --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">📚</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Kelas</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">
          {{ $student->class ? $student->class->class_level . ' ' . ($student->class->major->major_name ?? '') . ' ' . $student->class->classroom : '-' }}
        </div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:14px;width:20px;text-align:center;">🏫</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Jurusan</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">{{ $student->class->major->major_name ?? '-' }}</div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
      <span style="font-size:14px;width:20px;text-align:center;">👩‍🏫</span>
      <div>
        <div style="color:rgba(255,255,255,0.6);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Status</div>
        <div style="color:#fff;font-size:13px;font-weight:700;">{{ $student->status->status_name ?? $student->status->name ?? 'Aktif' }}</div>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4 anim-2">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#ecfeff;color:#0891b2"><i class="uil uil-clipboard-notes"></i></div>
      <div class="stat-label">Riwayat Bimbingan</div>
      <div class="stat-value">{{ $total_guidances }}</div>
      <div class="stat-sub">Total sesi</div>
      <div class="stat-accent" style="background:#0891b2"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><i class="uil uil-trophy"></i></div>
      <div class="stat-label">Prestasi Saya</div>
      <div class="stat-value">{{ $total_achievements }}</div>
      <div class="stat-sub">Tahun ini</div>
      <div class="stat-accent" style="background:#16a34a"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff7ed;color:#ea580c"><i class="uil uil-exclamation-triangle"></i></div>
      <div class="stat-label">Catatan Kasus</div>
      <div class="stat-value">{{ $total_cases }}</div>
      <div class="stat-sub">Total catatan</div>
      <div class="stat-accent" style="background:#ea580c"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f5f3ff;color:#7c3aed"><i class="uil uil-calendar-alt"></i></div>
      <div class="stat-label">Absensi Bulan Ini</div>
      <div class="stat-value">{{ $total_tidak_hadir ?? 0 }}</div>
      <div class="stat-sub">Sakit/Izin/Alpha bulan ini</div>
      <div class="stat-accent" style="background:#7c3aed"></div>
    </div>
  </div>
</div>

{{-- ROW: Bimbingan + Kasus --}}
<div class="row g-3 mb-3 anim-3">

  {{-- RIWAYAT BIMBINGAN --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>📋 Riwayat Bimbingan</h3>
        @can('Lihat Bimbingan')<a href="{{ route('guidance.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($my_guidances as $guidance)
      <div style="display:flex;align-items:flex-start;gap:12px;padding:13px 20px;border-bottom:1px solid var(--border);">
        <div style="width:36px;height:36px;border-radius:10px;background:#ecfeff;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">📝</div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $guidance->topic ?? 'Bimbingan' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $guidance->user->name ?? 'Guru BK' }}</div>
        </div>
        <div style="font-size:11px;color:var(--muted);white-space:nowrap;">{{ \Carbon\Carbon::parse($guidance->date)->format('d M') }}</div>
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-clipboard-notes" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Belum ada riwayat bimbingan
      </div>
      @endforelse
    </div>
  </div>

  {{-- CATATAN KASUS --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>⚠️ Catatan Kasus</h3>
        @can('Lihat Kasus')<a href="{{ route('case.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($my_cases as $case)
      <div style="display:flex;align-items:flex-start;gap:12px;padding:13px 20px;border-bottom:1px solid var(--border);">
        <div style="width:36px;height:36px;border-radius:10px;background:#fff7ed;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">⚠️</div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $case->problem ?? 'Kasus' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ \Carbon\Carbon::parse($case->date)->format('d M Y') }}</div>
        </div>
        @if(isset($case->status) && $case->status === 'Selesai')
          <span style="font-size:11px;font-weight:600;background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:20px;white-space:nowrap;">Selesai</span>
        @else
          <span style="font-size:11px;font-weight:600;background:#fff7ed;color:#9a3412;padding:3px 10px;border-radius:20px;white-space:nowrap;">Diproses</span>
        @endif
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-check-circle" style="font-size:2rem;color:#16a34a;display:block;margin-bottom:8px;"></i>
        Tidak ada catatan kasus
      </div>
      @endforelse
    </div>
  </div>

</div>

{{-- ROW: Prestasi + Absensi --}}
<div class="row g-3 anim-4">

  {{-- PRESTASI --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>🏆 Prestasi Saya</h3>
        @can('Lihat Prestasi')<a href="{{ route('achievement.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($my_achievements as $achievement)
      <div style="display:flex;align-items:center;gap:12px;padding:13px 20px;border-bottom:1px solid var(--border);">
        <div style="width:36px;height:36px;border-radius:10px;background:#fefce8;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🥇</div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $achievement->name ?? 'Prestasi' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ \Carbon\Carbon::parse($achievement->date)->format('d M Y') }}</div>
        </div>
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-trophy" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Belum ada prestasi tercatat
      </div>
      @endforelse
    </div>
  </div>

  {{-- REKAP ABSENSI --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>📅 Rekap Absensi</h3>
        @can('Lihat Absensi')<a href="{{ route('attendance.index') }}">Lihat Semua</a>@endcan
      </div>
      @forelse($my_attendances as $att)
      <div style="display:flex;align-items:center;gap:12px;padding:11px 20px;border-bottom:1px solid var(--border);">
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</div>
        </div>
        @php $s = $att->presence_status ?? 'Hadir'; @endphp
        @if($s === 'Hadir')
          <span style="font-size:11px;font-weight:600;background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:20px;">Hadir</span>
        @elseif($s === 'Sakit')
          <span style="font-size:11px;font-weight:600;background:#fef9c3;color:#854d0e;padding:3px 10px;border-radius:20px;">Sakit</span>
        @elseif($s === 'Ijin')
          <span style="font-size:11px;font-weight:600;background:#eff6ff;color:#1d4ed8;padding:3px 10px;border-radius:20px;">Ijin</span>
        @else
          <span style="font-size:11px;font-weight:600;background:#fef2f2;color:#991b1b;padding:3px 10px;border-radius:20px;">Alpa</span>
        @endif
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-calendar-alt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Belum ada data absensi
      </div>
      @endforelse
    </div>
  </div>

</div>

@endsection
