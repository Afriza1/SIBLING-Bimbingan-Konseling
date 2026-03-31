@extends('layouts.dashboard')

@section('content')

{{-- BANNER --}}
<div class="dash-banner anim-1" style="background: linear-gradient(135deg, #065f46 0%, #059669 55%, #34d399 100%); margin-bottom:24px;">
  <div>
    <h2><span>Selamat Pagi,</span><br>{{ auth()->user()->name }}</h2>
    <p>Semoga harimu menyenangkan.<br>Kelola sesi bimbingan Anda hari ini.</p>
    <div class="banner-badge">📋 GURU BK</div>
  </div>
  <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:16px 20px;flex-shrink:0;">
    <svg width="70" height="70" viewBox="0 0 80 80" fill="none">
      <rect x="8" y="10" width="50" height="60" rx="6" fill="rgba(255,255,255,0.2)"/>
      <rect x="15" y="18" width="36" height="4" rx="2" fill="rgba(255,255,255,0.7)"/>
      <rect x="15" y="26" width="28" height="3" rx="2" fill="rgba(255,255,255,0.5)"/>
      <rect x="15" y="33" width="32" height="3" rx="2" fill="rgba(255,255,255,0.5)"/>
      <rect x="15" y="40" width="24" height="3" rx="2" fill="rgba(255,255,255,0.4)"/>
      <rect x="15" y="47" width="28" height="3" rx="2" fill="rgba(255,255,255,0.4)"/>
      <circle cx="62" cy="24" r="12" fill="rgba(255,255,255,0.2)"/>
      <path d="M57 24l4 4 7-7" stroke="rgba(255,255,255,0.9)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4 anim-2">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="uil uil-clipboard-notes"></i></div>
      <div class="stat-label">Total Bimbingan</div>
      <div class="stat-value">{{ $total_guidances }}</div>
      <div class="stat-sub">Semua sesi</div>
      <div class="stat-accent" style="background:#2563eb"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff7ed;color:#ea580c"><i class="uil uil-exclamation-triangle"></i></div>
      <div class="stat-label">Total Kasus</div>
      <div class="stat-value">{{ $total_cases }}</div>
      <div class="stat-sub">Aktif ditangani</div>
      <div class="stat-accent" style="background:#ea580c"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#ecfeff;color:#0891b2"><i class="uil uil-schedule"></i></div>
      <div class="stat-label">Booking Masuk</div>
      <div class="stat-value">{{ $total_bookings }}</div>
      <div class="stat-sub">Menunggu konfirmasi</div>
      <div class="stat-accent" style="background:#0891b2"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><i class="uil uil-calendar-alt"></i></div>
      <div class="stat-label">Absensi Hari Ini</div>
      <div class="stat-value">{{ $total_attendances }}</div>
      <div class="stat-sub">Siswa tidak hadir</div>
      <div class="stat-accent" style="background:#16a34a"></div>
    </div>
  </div>
</div>

{{-- BOTTOM: Booking + Bimbingan Terbaru --}}
<div class="row g-3 anim-3">

  {{-- BOOKING MENUNGGU --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>🗓️ Booking Menunggu</h3>
        @can('Lihat Booking Bimbingan')
        <a href="{{ route('guidanceBooking.index') }}">Lihat Semua</a>
        @endcan
      </div>
      @forelse($pending_bookings as $booking)
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);">
        <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
          {{ strtoupper(substr($booking->student->name ?? 'S', 0, 2)) }}
        </div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $booking->student->name ?? 'Siswa' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;">
            {{ $booking->student->class->name ?? '-' }} · {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}
          </div>
        </div>
        @php
          $diff = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($booking->date), false);
        @endphp
        @if($diff === 0)
          <span style="font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e;padding:3px 10px;border-radius:20px;">Hari Ini</span>
        @elseif($diff > 0)
          <span style="font-size:11px;font-weight:700;background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:20px;">{{ \Carbon\Carbon::parse($booking->date)->format('d M') }}</span>
        @else
          <span style="font-size:11px;font-weight:700;background:#fef2f2;color:#991b1b;padding:3px 10px;border-radius:20px;">Terlambat</span>
        @endif
      </div>
      @empty
      <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
        <i class="uil uil-calendar-slash" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Tidak ada booking menunggu
      </div>
      @endforelse
    </div>
  </div>

  {{-- BIMBINGAN TERBARU --}}
  <div class="col-lg-6">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3>📝 Bimbingan Terbaru</h3>
        @can('Lihat Bimbingan')
        <a href="{{ route('guidance.index') }}">Lihat Semua</a>
        @endcan
      </div>
      @forelse($recent_guidances as $guidance)
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);">
        <div style="width:10px;height:10px;border-radius:50%;background:#059669;flex-shrink:0;"></div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;">{{ $guidance->student->name ?? 'Siswa' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $guidance->topic ?? 'Bimbingan' }}</div>
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

</div>

@endsection
