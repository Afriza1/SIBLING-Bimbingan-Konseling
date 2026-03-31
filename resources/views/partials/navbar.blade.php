<div class="app-topbar">

  {{-- Toggle sidebar --}}
  <div class="topbar-toggle" id="sidebarToggle">
    <i class="bx bx-menu"></i>
  </div>

  {{-- Page title --}}
  <div class="topbar-title">
    <h5>
      @if($active === 'home') Beranda
      @elseif($active === 'student') Data Siswa
      @elseif($active === 'user') Data User
      @elseif($active === 'major') Jurusan
      @elseif($active === 'class') Kelas
      @elseif($active === 'role') Role
      @elseif($active === 'status') Status
      @elseif($active === 'assessment') Asesmen
      @elseif($active === 'guidance_booking') Booking Bimbingan
      @elseif($active === 'guidance') Bimbingan
      @elseif($active === 'case') Kasus
      @elseif($active === 'attendance') Rekap Absensi
      @elseif($active === 'job_vacancy') Karir
      @elseif($active === 'achievement') Prestasi
      @elseif($active === 'student_assessment') Asesmen Siswa
      @elseif($active === 'autentifikasi') Autentifikasi
      @elseif($active === 'permission') Permission
      @elseif($active === 'settings') Pengaturan
      @else Dashboard
      @endif
    </h5>
    <small>SMKN 7 Negeri Jember</small>
  </div>

  {{-- Right side --}}
  <div class="topbar-right">

    {{-- Datetime --}}
    <div class="topbar-datetime">
      <span id="topbar-date"></span>
      <span id="topbar-time"></span>
    </div>

    <div class="topbar-divider"></div>

    {{-- Dark mode toggle --}}
    <div class="topbar-theme" id="themeToggle" title="Ganti tema">
      <i class="uil uil-moon"></i>
    </div>

    {{-- Profile dropdown --}}
    <div class="topbar-profile" id="topbarProfile" style="position:relative;">
      @if(auth()->user()->photo)
        <img src="{{ route('user.showImage', auth()->user()->id) }}" alt="foto">
      @else
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" alt="foto">
      @endif
      <div class="topbar-profile-info">
        <span class="topbar-profile-name">{{ Str::limit(auth()->user()->name, 16) }}</span>
        <span class="topbar-profile-role">{{ auth()->user()->getRoleNames()->first() ?? 'Tidak Ada Role' }}</span>
      </div>
      <i class="uil uil-angle-down topbar-profile-arrow"></i>

      {{-- Dropdown menu --}}
      <div class="topbar-dropdown" id="topbarDropdown">
        <a href="/settings">
          <i class="uil uil-user"></i> My Profile
        </a>
        <a href="/">
          <i class="uil uil-estate"></i> Landing Page
        </a>
        <hr>
        <a href="{{ route('logout') }}" class="danger"
          onclick="event.preventDefault(); document.getElementById('topbar-logout').submit();">
          <i class="uil uil-sign-out-alt"></i> Sign Out
        </a>
        <form id="topbar-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </div>
    </div>

  </div>
</div>
