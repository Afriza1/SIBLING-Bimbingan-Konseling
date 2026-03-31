@extends('layouts.dashboard')

@section('content')

{{-- BANNER --}}
<div class="dash-banner anim-1" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #3b82f6 100%); margin-bottom:24px;">
  <div>
    <h2><span>Selamat Datang,</span><br>{{ auth()->user()->name }}</h2>
    <p>Panel Administrator — Kelola seluruh sistem BK SMKN 7 Jember.</p>
    <div class="banner-badge">⚙️ ADMINISTRATOR</div>
  </div>
  <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:16px 20px;flex-shrink:0;">
    <svg width="70" height="70" viewBox="0 0 80 80" fill="none">
      <rect x="10" y="8" width="60" height="44" rx="6" fill="rgba(255,255,255,0.2)"/>
      <rect x="16" y="14" width="48" height="32" rx="3" fill="rgba(255,255,255,0.15)"/>
      <rect x="20" y="18" width="30" height="3" rx="2" fill="rgba(255,255,255,0.7)"/>
      <rect x="20" y="24" width="22" height="3" rx="2" fill="rgba(255,255,255,0.5)"/>
      <circle cx="55" cy="28" r="10" fill="rgba(255,255,255,0.2)"/>
      <path d="M51 28l3 3 6-6" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</div>

{{-- STAT CARDS ROW 1 --}}
<div class="row g-3 mb-3 anim-2">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="uil uil-users-alt"></i></div>
      <div class="stat-label">Total Siswa</div>
      <div class="stat-value">{{ $total_students }}</div>
      <div class="stat-sub">Terdaftar aktif</div>
      <div class="stat-accent" style="background:#2563eb"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><i class="uil uil-clipboard-notes"></i></div>
      <div class="stat-label">Total Bimbingan</div>
      <div class="stat-value">{{ $total_guidances }}</div>
      <div class="stat-sub">Semua sesi</div>
      <div class="stat-accent" style="background:#16a34a"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff7ed;color:#ea580c"><i class="uil uil-exclamation-triangle"></i></div>
      <div class="stat-label">Total Kasus</div>
      <div class="stat-value">{{ $total_cases }}</div>
      <div class="stat-sub">Semua kasus</div>
      <div class="stat-accent" style="background:#ea580c"></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f5f3ff;color:#7c3aed"><i class="uil uil-user-check"></i></div>
      <div class="stat-label">Total User</div>
      <div class="stat-value">{{ $total_users }}</div>
      <div class="stat-sub">Semua role</div>
      <div class="stat-accent" style="background:#7c3aed"></div>
    </div>
  </div>
</div>

{{-- STAT CARDS ROW 2 --}}
<div class="row g-3 mb-4 anim-3">
  <div class="col-xl-4 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#ecfeff;color:#0891b2"><i class="uil uil-notes"></i></div>
      <div class="stat-label">Total Loker</div>
      <div class="stat-value">{{ $total_job_vacancies }}</div>
      <div class="stat-sub">Lowongan tersedia</div>
      <div class="stat-accent" style="background:#0891b2"></div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fef9c3;color:#ca8a04"><i class="uil uil-trophy"></i></div>
      <div class="stat-label">Total Prestasi</div>
      <div class="stat-value">{{ $total_achievements }}</div>
      <div class="stat-sub">Semua prestasi</div>
      <div class="stat-accent" style="background:#ca8a04"></div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fef2f2;color:#dc2626"><i class="uil uil-calendar-alt"></i></div>
      <div class="stat-label">Total Absensi</div>
      <div class="stat-value">{{ $total_attendances }}</div>
      <div class="stat-sub">Rekap kehadiran</div>
      <div class="stat-accent" style="background:#dc2626"></div>
    </div>
  </div>
</div>

{{-- CHARTS --}}
<div class="row g-3 anim-4">
  <div class="col-lg-6">
    <div class="dash-card mb-3">
      <div class="dash-card-header"><h3>Grafik Bimbingan per Hari (Bulan Ini)</h3></div>
      <div style="padding:16px"><canvas id="guidanceChart" height="160"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="dash-card mb-3">
      <div class="dash-card-header"><h3>Grafik Kasus per Hari (Bulan Ini)</h3></div>
      <div style="padding:16px"><canvas id="caseChart" height="160"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="dash-card mb-3">
      <div class="dash-card-header"><h3>Grafik Absensi per Hari (Bulan Ini)</h3></div>
      <div style="padding:16px"><canvas id="attendanceChart" height="160"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="dash-card mb-3">
      <div class="dash-card-header"><h3>Grafik Login per Hari (Bulan Ini)</h3></div>
      <div style="padding:16px"><canvas id="dailyLoginsChart" height="160"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="dash-card mb-3">
      <div class="dash-card-header"><h3>Grafik Karir per Bulan (Tahun Ini)</h3></div>
      <div style="padding:16px"><canvas id="careerChart" height="160"></canvas></div>
    </div>
  </div>
</div>

{{-- RIWAYAT LOGIN --}}
<div class="row g-3 mt-2 anim-5">
  <div class="col-12">
    <div class="dash-card">
      <div class="dash-card-header">
        <h3><i class="uil uil-history me-1"></i> Riwayat Login Pengguna</h3>
      </div>
      <div style="padding:16px;">
        <div class="table-responsive">
          <table class="table table-hover table-sm" style="font-size:13px;">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Waktu Login</th>
                <th>IP Address</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recent_logins as $i => $log)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if(optional($log->user)->photo)
                      <img src="{{ asset('storage/' . $log->user->photo) }}"
                        style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                    @else
                      <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($log->user)->name ?? '?') }}&background=random&size=28"
                        style="width:28px;height:28px;border-radius:50%;">
                    @endif
                    <span>{{ optional($log->user)->name ?? '-' }}</span>
                  </div>
                </td>
                <td>
                  @php
                    $role = optional($log->user)->roles->first()?->name ?? '-';
                    $roleColor = match($role) {
                      'Admin'      => 'primary',
                      'Guru BK'    => 'success',
                      'Wali Kelas' => 'info',
                      'Siswa'      => 'warning',
                      default      => 'secondary',
                    };
                  @endphp
                  <span class="badge bg-{{ $roleColor }}">{{ $role }}</span>
                </td>
                <td>
                  <span>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</span>
                  <span class="text-muted ms-1">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</span>
                </td>
                <td><span class="text-muted">{{ $log->ip_address ?? '-' }}</span></td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted py-3">Belum ada riwayat login</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const opts = { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } };
  new Chart(document.getElementById('guidanceChart'), { type: 'line', data: { labels: @json(range(1, $days_in_month)), datasets: [{ data: @json($guidances_per_day), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] }, options: opts });
  new Chart(document.getElementById('caseChart'), { type: 'line', data: { labels: @json(range(1, $days_in_month)), datasets: [{ data: @json($cases_per_day), borderColor: '#ea580c', backgroundColor: 'rgba(234,88,12,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] }, options: opts });
  new Chart(document.getElementById('attendanceChart'), { type: 'line', data: { labels: @json(range(1, $days_in_month)), datasets: [{ data: @json($attendances_per_day), borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] }, options: opts });
  new Chart(document.getElementById('dailyLoginsChart'), { type: 'line', data: { labels: @json(range(1, $days_in_month)), datasets: [{ data: @json($logins_per_day), borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] }, options: opts });
  new Chart(document.getElementById('careerChart'), { type: 'bar', data: { labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'], datasets: [{ data: @json($careers_per_month), backgroundColor: 'rgba(8,145,178,0.7)', borderColor: '#0891b2', borderWidth: 1, borderRadius: 6 }] }, options: opts });
</script>
@endpush
