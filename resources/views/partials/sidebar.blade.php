<div class="app-sidebar" id="appSidebar">

  {{-- LOGO --}}
  <div style="padding: 20px 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.08);">
    <div style="display:flex; align-items:center; gap:10px;">
      <img src="/img/app_logo.png" width="32" height="32" alt="Logo" style="border-radius:8px; object-fit:contain;">
      <div>
        <div style="color:#fff; font-size:13px; font-weight:800; line-height:1.2;">SMKN 7 Jember</div>
        <div style="color:rgba(255,255,255,0.4); font-size:10px;">Sistem BK</div>
      </div>
    </div>
  </div>

  {{-- MENU --}}
  <nav style="flex:1; padding: 16px 12px; overflow-y:auto;">

    {{-- Beranda --}}
    <a href="{{ route('home') }}" class="sb-item {{ $active === 'home' ? 'sb-active' : '' }}">
      <i class="uil uil-apps sb-icon"></i>
      <span>Beranda</span>
    </a>

    {{-- ──── ADMIN ──── --}}
    @hasrole('Admin')

      <div class="sb-label">Master Data</div>

      @can('Lihat Siswa')
      <a href="{{ route('student.index') }}" class="sb-item {{ $active === 'student' ? 'sb-active' : '' }}">
        <i class="uil uil-users-alt sb-icon"></i><span>Siswa</span>
      </a>
      @endcan

      @can('Lihat User')
      <a href="{{ route('user.index') }}" class="sb-item {{ $active === 'user' ? 'sb-active' : '' }}">
        <i class="uil uil-user-circle sb-icon"></i><span>User</span>
      </a>
      @endcan

      @can('Lihat Jurusan')
      <a href="{{ route('major.index') }}" class="sb-item {{ $active === 'major' ? 'sb-active' : '' }}">
        <i class="uil uil-book-open sb-icon"></i><span>Jurusan</span>
      </a>
      @endcan

      @can('Lihat Kelas')
      <a href="{{ route('class.index') }}" class="sb-item {{ $active === 'class' ? 'sb-active' : '' }}">
        <i class="uil uil-building sb-icon"></i><span>Kelas</span>
      </a>
      @endcan

      @can('Lihat Role')
      <a href="{{ route('role.index') }}" class="sb-item {{ $active === 'role' ? 'sb-active' : '' }}">
        <i class="uil uil-shield sb-icon"></i><span>Role</span>
      </a>
      @endcan

      @can('Lihat Status')
      <a href="{{ route('status.index') }}" class="sb-item {{ $active === 'status' ? 'sb-active' : '' }}">
        <i class="uil uil-tag-alt sb-icon"></i><span>Status</span>
      </a>
      @endcan

      @can('Lihat Asesmen')
      <a href="{{ route('assessment.index') }}" class="sb-item {{ $active === 'assessment' ? 'sb-active' : '' }}">
        <i class="uil uil-clipboard-notes sb-icon"></i><span>Asesmen</span>
      </a>
      @endcan

      <div class="sb-label">Operasional</div>

      @can('Lihat Booking Bimbingan')
      <a href="{{ route('guidanceBooking.index') }}" class="sb-item {{ $active === 'guidance_booking' ? 'sb-active' : '' }}">
        <i class="uil uil-schedule sb-icon"></i><span>Booking Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Bimbingan')
      <a href="{{ route('guidance.index') }}" class="sb-item {{ $active === 'guidance' ? 'sb-active' : '' }}">
        <i class="uil uil-notes sb-icon"></i><span>Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Kasus')
      <a href="{{ route('case.index') }}" class="sb-item {{ $active === 'case' ? 'sb-active' : '' }}">
        <i class="uil uil-exclamation-triangle sb-icon"></i><span>Kasus</span>
      </a>
      @endcan

      @can('Lihat Absensi')
      <a href="{{ route('attendance.index') }}" class="sb-item {{ $active === 'attendance' ? 'sb-active' : '' }}">
        <i class="uil uil-calendar-alt sb-icon"></i><span>Rekap Absensi</span>
      </a>
      @endcan

      @can('Lihat Keterlambatan')
        <a href="{{ route('lateness.index') }}" class="sb-item {{ $active === 'lateness' ? 'sb-active' : '' }}">
            <i class="uil uil-clock sb-icon"></i><span>Keterlambatan</span>
        </a>
      @endcan

      @can('Lihat Loker')
      <a href="{{ route('jobVacancy.index') }}" class="sb-item {{ $active === 'job_vacancy' ? 'sb-active' : '' }}">
        <i class="uil uil-briefcase-alt sb-icon"></i><span>Karir</span>
      </a>
      @endcan

      @can('Lihat Prestasi')
      <a href="{{ route('achievement.index') }}" class="sb-item {{ $active === 'achievement' ? 'sb-active' : '' }}">
        <i class="uil uil-trophy sb-icon"></i><span>Prestasi</span>
      </a>
      @endcan

      @can('Lihat Asesmen Siswa')
      <a href="{{ route('student_assessment.index') }}" class="sb-item {{ $active === 'student_assessment' ? 'sb-active' : '' }}">
        <i class="uil uil-file-check-alt sb-icon"></i><span>Asesmen Siswa</span>
      </a>
      @endcan

      <div class="sb-label">Sistem</div>

      @can('Lihat Autentifikasi')
      <a href="{{ route('autentifikasi.index') }}" class="sb-item {{ $active === 'autentifikasi' ? 'sb-active' : '' }}">
        <i class="uil uil-shield-check sb-icon"></i><span>Autentifikasi</span>
        @php $pendingCount = \App\Models\User::where('account_status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
        @endif
      </a>
      @endcan

      @can('Lihat Perizinan')
      <a href="{{ route('permission.index') }}" class="sb-item {{ $active === 'permission' ? 'sb-active' : '' }}">
        <i class="uil uil-lock-access sb-icon"></i><span>Permission</span>
      </a>
      @endcan



    @endhasrole

    {{-- ──── GURU BK ──── --}}
    @hasrole('Guru BK')

      <div class="sb-label">Bimbingan</div>

      @can('Lihat Booking Bimbingan')
      <a href="{{ route('guidanceBooking.index') }}" class="sb-item {{ $active === 'guidance_booking' ? 'sb-active' : '' }}">
        <i class="uil uil-schedule sb-icon"></i><span>Booking Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Bimbingan')
      <a href="{{ route('guidance.index') }}" class="sb-item {{ $active === 'guidance' ? 'sb-active' : '' }}">
        <i class="uil uil-notes sb-icon"></i><span>Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Kasus')
      <a href="{{ route('case.index') }}" class="sb-item {{ $active === 'case' ? 'sb-active' : '' }}">
        <i class="uil uil-exclamation-triangle sb-icon"></i><span>Kasus</span>
      </a>
      @endcan

      <div class="sb-label">Data Siswa</div>

      @can('Lihat Siswa')
      <a href="{{ route('student.index') }}" class="sb-item {{ $active === 'student' ? 'sb-active' : '' }}">
        <i class="uil uil-users-alt sb-icon"></i><span>Siswa</span>
      </a>
      @endcan

      @can('Lihat Absensi')
      <a href="{{ route('attendance.index') }}" class="sb-item {{ $active === 'attendance' ? 'sb-active' : '' }}">
        <i class="uil uil-calendar-alt sb-icon"></i><span>Rekap Absensi</span>
      </a>
      @endcan

      @can('Lihat Keterlambatan')
        <a href="{{ route('lateness.index') }}" class="sb-item {{ $active === 'lateness' ? 'sb-active' : '' }}">
        <i class="uil uil-clock sb-icon"></i><span>Keterlambatan</span>
        </a>
        @endcan

      @can('Lihat Prestasi')
      <a href="{{ route('achievement.index') }}" class="sb-item {{ $active === 'achievement' ? 'sb-active' : '' }}">
        <i class="uil uil-trophy sb-icon"></i><span>Prestasi</span>
      </a>
      @endcan

      @can('Lihat Loker')
      <a href="{{ route('jobVacancy.index') }}" class="sb-item {{ $active === 'job_vacancy' ? 'sb-active' : '' }}">
        <i class="uil uil-briefcase-alt sb-icon"></i><span>Karir</span>
      </a>
      @endcan

      @can('Lihat Asesmen Siswa')
      <a href="{{ route('student_assessment.index') }}" class="sb-item {{ $active === 'student_assessment' ? 'sb-active' : '' }}">
        <i class="uil uil-file-check-alt sb-icon"></i><span>Asesmen Siswa</span>
      </a>
      @endcan

    @endhasrole

    {{-- ──── WALI KELAS ──── --}}
    @hasrole('Wali Kelas')

      <div class="sb-label">Data Kelas</div>

      @can('Lihat Bimbingan')
      <a href="{{ route('guidance.index') }}" class="sb-item {{ $active === 'guidance' ? 'sb-active' : '' }}">
        <i class="uil uil-notes sb-icon"></i><span>Bimbingan Siswa</span>
      </a>
      @endcan

      @can('Lihat Kasus')
      <a href="{{ route('case.index') }}" class="sb-item {{ $active === 'case' ? 'sb-active' : '' }}">
        <i class="uil uil-exclamation-triangle sb-icon"></i><span>Kasus Siswa</span>
      </a>
      @endcan

      @can('Lihat Prestasi')
      <a href="{{ route('achievement.index') }}" class="sb-item {{ $active === 'achievement' ? 'sb-active' : '' }}">
        <i class="uil uil-trophy sb-icon"></i><span>Prestasi Siswa</span>
      </a>
      @endcan

      @can('Lihat Absensi')
      <a href="{{ route('attendance.index') }}" class="sb-item {{ $active === 'attendance' ? 'sb-active' : '' }}">
        <i class="uil uil-calendar-alt sb-icon"></i><span>Rekap Absensi</span>
      </a>
      @endcan

      @can('Lihat Keterlambatan')
        <a href="{{ route('lateness.index') }}" class="sb-item {{ $active === 'lateness' ? 'sb-active' : '' }}">
        <i class="uil uil-clock sb-icon"></i><span>Keterlambatan Siswa</span>
        </a>
        @endcan

      @can('Lihat Siswa')
      <a href="{{ route('student.index') }}" class="sb-item {{ $active === 'student' ? 'sb-active' : '' }}">
        <i class="uil uil-users-alt sb-icon"></i><span>Daftar Siswa</span>
      </a>
      @endcan

    @endhasrole

    {{-- ──── SISWA ──── --}}
    @hasrole('Siswa')

      <div class="sb-label">Data Saya</div>

      {{-- BOOKING BIMBINGAN: arahkan ke form khusus siswa --}}
      @can('Lihat Booking Bimbingan')
      <a href="{{ route('guidanceBooking.form') }}" class="sb-item {{ $active === 'guidance_booking' ? 'sb-active' : '' }}">
        <i class="uil uil-schedule sb-icon"></i><span>Booking Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Bimbingan')
      <a href="{{ route('guidance.index') }}" class="sb-item {{ $active === 'guidance' ? 'sb-active' : '' }}">
        <i class="uil uil-notes sb-icon"></i><span>Riwayat Bimbingan</span>
      </a>
      @endcan

      @can('Lihat Kasus')
      <a href="{{ route('case.index') }}" class="sb-item {{ $active === 'case' ? 'sb-active' : '' }}">
        <i class="uil uil-exclamation-triangle sb-icon"></i><span>Catatan Kasus</span>
      </a>
      @endcan

      @can('Lihat Prestasi')
      <a href="{{ route('achievement.index') }}" class="sb-item {{ $active === 'achievement' ? 'sb-active' : '' }}">
        <i class="uil uil-trophy sb-icon"></i><span>Prestasi Saya</span>
      </a>
      @endcan

      @can('Lihat Absensi')
      <a href="{{ route('attendance.index') }}" class="sb-item {{ $active === 'attendance' ? 'sb-active' : '' }}">
        <i class="uil uil-calendar-alt sb-icon"></i><span>Rekap Absensi</span>
      </a>
      @endcan

      @can('Lihat Asesmen Siswa')
        <a href="{{ route('student_assessment.index') }}" class="sb-item {{ $active === 'student_assessment' ? 'sb-active' : '' }}">
            <i class="uil uil-file-check-alt sb-icon"></i><span>Asesmen</span>
        </a>
      @endcan

      @can('Lihat Keterlambatan')
        <a href="{{ route('lateness.index') }}" class="sb-item {{ $active === 'lateness' ? 'sb-active' : '' }}">
        <i class="uil uil-clock sb-icon"></i><span>Keterlambatan Saya</span>
        </a>
        @endcan

      @can('Lihat Loker')
      <a href="{{ route('jobVacancy.index') }}" class="sb-item {{ $active === 'job_vacancy' ? 'sb-active' : '' }}">
        <i class="uil uil-briefcase-alt sb-icon"></i><span>Info Karir</span>
      </a>
      @endcan

    @endhasrole

    {{-- ──── SEMUA ROLE ──── --}}
    <div class="sb-label">Akun</div>
    <a href="/settings" class="sb-item {{ $active === 'settings' ? 'sb-active' : '' }}">
      <i class="uil uil-setting sb-icon"></i><span>Pengaturan</span>
    </a>

  </nav>

  {{-- PROFILE --}}
  <div style="padding: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
    <div style="display:flex; align-items:center; gap:10px; padding:10px; background:rgba(255,255,255,0.06); border-radius:10px;">
      @if(auth()->user()->photo)
        <img src="{{ route('user.showImage', auth()->user()->id) }}" alt="foto" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
      @else
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" alt="foto" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
      @endif
      <div style="flex:1;min-width:0;">
        <div style="color:#fff;font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
        <div style="color:rgba(255,255,255,0.4);font-size:11px;">{{ auth()->user()->getRoleNames()->first() ?? 'Tidak Ada Role' }}</div>
      </div>
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('sidebar-logout').submit();" style="color:rgba(255,255,255,0.4);font-size:18px;text-decoration:none;flex-shrink:0;" title="Logout">
        <i class="uil uil-signout"></i>
      </a>
      <form id="sidebar-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
  </div>

</div>

<style>
  .sb-label {
    color: rgba(255,255,255,0.28);
    font-size: 10px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase;
    padding: 12px 12px 6px; margin-top: 4px;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .sb-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 8px;
    color: rgba(255,255,255,0.55);
    font-size: 13px; font-weight: 500;
    text-decoration: none; margin-bottom: 2px;
    transition: all 0.15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .sb-item:hover { background: rgba(255,255,255,0.08); color: #fff; text-decoration: none; }
  .sb-item.sb-active { background: #2563eb; color: #fff; }
  .sb-icon { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
</style>
