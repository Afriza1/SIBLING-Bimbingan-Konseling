@extends('layouts.dashboard')
@section('content')

<div style="max-width:600px;margin:0 auto;padding-top:24px;">

  {{-- BANNER --}}
  <div style="background:linear-gradient(135deg,#0369a1,#0891b2);border-radius:14px;padding:24px 28px;margin-bottom:24px;color:#fff;">
    <div style="font-size:20px;font-weight:800;margin-bottom:6px;">📅 Ajukan Booking Bimbingan</div>
    <div style="font-size:13px;opacity:0.85;">Pilih tanggal dan waktu yang tersedia, Guru BK akan mengkonfirmasi jadwal Anda.</div>
    @if($student)
    <div style="margin-top:12px;background:rgba(255,255,255,0.15);border-radius:8px;padding:10px 14px;font-size:13px;">
      <strong>{{ $student->name }}</strong> · {{ $student->class->name ?? '-' }}
    </div>
    @endif
  </div>

  {{-- SUCCESS / ERROR --}}
  @if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    ✅ {{ session('success') }}
  </div>
  @endif
  @if($errors->any())
  <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    ❌ {{ $errors->first() }}
  </div>
  @endif

  {{-- FORM --}}
  <div class="dash-card" style="padding:24px;">
    <form action="{{ route('guidanceBooking.store') }}" method="POST">
      @csrf
      <input type="hidden" name="status" value="pending">

      {{-- Nama otomatis dari siswa --}}
      <div style="margin-bottom:16px;">
        <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Nama</label>
        <input type="text" name="name" class="form-control"
          value="{{ $student->name ?? auth()->user()->name }}"
          readonly style="background:var(--bg);font-weight:600;">
      </div>

      {{-- Nomor WhatsApp --}}
      <div style="margin-bottom:16px;">
        <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Nomor WhatsApp <span style="color:#dc2626;">*</span></label>
        <input type="number" name="phone_number" class="form-control"
          placeholder="Contoh: 08123456789" required
          value="{{ old('phone_number', $student->phone_number ?? '') }}">
      </div>

      {{-- Tanggal --}}
      <div style="margin-bottom:16px;">
        <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Tanggal Bimbingan <span style="color:#dc2626;">*</span></label>
        <input type="date" name="booking_date" class="form-control" required
          min="{{ now()->addDay()->format('Y-m-d') }}"
          value="{{ old('booking_date') }}">
      </div>

      {{-- Pilih Waktu --}}
      <div style="margin-bottom:24px;">
        <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:10px;">Pilih Waktu <span style="color:#dc2626;">*</span></label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          @foreach($slotInfo as $slot)
          <label style="border:2px solid {{ $slot['full'] ? '#e2e8f0' : '#2563eb' }};border-radius:10px;padding:12px 14px;cursor:{{ $slot['full'] ? 'not-allowed' : 'pointer' }};opacity:{{ $slot['full'] ? '0.5' : '1' }};display:flex;align-items:center;gap:10px;">
            <input type="radio" name="booking_time" value="{{ $slot['time'] }}" {{ $slot['full'] ? 'disabled' : '' }} required style="accent-color:#2563eb;">
            <div>
              <div style="font-weight:700;font-size:14px;">{{ $slot['time'] }}</div>
              <div style="font-size:11px;color:{{ $slot['full'] ? '#dc2626' : '#16a34a' }};">
                {{ $slot['full'] ? 'Penuh' : 'Sisa ' . $slot['remaining'] . ' slot' }}
              </div>
            </div>
          </label>
          @endforeach
        </div>
      </div>

      <button type="submit" style="width:100%;background:#2563eb;color:#fff;border:none;border-radius:10px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;">
        📨 Ajukan Booking
      </button>
    </form>
  </div>

  {{-- RIWAYAT BOOKING --}}
  <div class="dash-card" style="margin-top:20px;">
    <div class="dash-card-header"><h3>📋 Riwayat Booking Saya</h3></div>
    @php
      $myBookings = \App\Models\GuidanceBooking::where('name', $student->name ?? auth()->user()->name)->latest()->take(5)->get();
    @endphp
    @forelse($myBookings as $b)
    <div style="display:flex;align-items:center;gap:12px;padding:13px 20px;border-bottom:1px solid var(--border);">
      <div style="flex:1;">
        <div style="font-size:13px;font-weight:600;">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y · H:i') }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $b->phone_number }}</div>
      </div>
      @if($b->status === 'pending')
        <span style="font-size:11px;font-weight:600;background:#fef9c3;color:#854d0e;padding:3px 10px;border-radius:20px;">Menunggu</span>
      @elseif($b->status === 'confirmed')
        <span style="font-size:11px;font-weight:600;background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:20px;">Terkonfirmasi</span>
      @else
        <span style="font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:20px;">Selesai</span>
      @endif
    </div>
    @empty
    <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
      <i class="uil uil-calendar-slash" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
      Belum ada riwayat booking
    </div>
    @endforelse
  </div>

</div>
@endsection
