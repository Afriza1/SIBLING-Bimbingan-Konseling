<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/img/app_logo.png">

  {{-- Icon Libraries --}}
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/solid.css" />
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />

  {{-- DataTables --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- Calendar --}}
  <link rel="stylesheet" href="css/calendar.css">

  @stack('styles')

  <title>Dashboard | SMKN 7 Negeri Jember</title>

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  <style>
    :root {
      --sidebar-bg: #1a2340;
      --sidebar-width: 250px;
      --topbar-height: 60px;
      --bg: #f0f4f8;
      --card-bg: #ffffff;
      --text: #1e293b;
      --muted: #64748b;
      --border: #e2e8f0;
      --primary: #2563eb;
      --font: 'Plus Jakarta Sans', sans-serif;
      --radius: 14px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: var(--font) !important;
      background: var(--bg);
      color: var(--text);
    }

    /* ── WRAPPER ── */
    .app-wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* ── SIDEBAR ── */
    .app-sidebar {
      width: var(--sidebar-width);
      min-height: 100vh;
      background: var(--sidebar-bg);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: fixed;
      top: 0; left: 0;
      z-index: 100;
      transition: transform 0.3s ease;
    }
    .app-sidebar.collapsed {
      transform: translateX(calc(-1 * var(--sidebar-width)));
    }

    /* ── MAIN ── */
    .app-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }
    .app-main.expanded {
      margin-left: 0;
    }

    /* ── TOPBAR ── */
    .app-topbar {
      height: var(--topbar-height);
      background: var(--card-bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 24px;
      gap: 16px;
      position: sticky;
      top: 0;
      z-index: 99;
    }

    .topbar-toggle {
      width: 36px; height: 36px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--bg);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 18px; color: var(--muted);
      transition: all 0.2s;
      flex-shrink: 0;
    }
    .topbar-toggle:hover { background: var(--border); color: var(--text); }

    .topbar-title { flex: 1; }
    .topbar-title h5 { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
    .topbar-title small { font-size: 12px; color: var(--muted); }

    .topbar-right { display: flex; align-items: center; gap: 12px; }

    .topbar-datetime {
      text-align: right;
      display: flex; flex-direction: column;
    }
    .topbar-datetime #topbar-date { font-size: 12px; color: var(--muted); font-weight: 500; }
    .topbar-datetime #topbar-time { font-size: 13px; color: var(--text); font-weight: 700; }

    .topbar-divider { width: 1px; height: 28px; background: var(--border); }

    .topbar-theme {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--bg); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 16px; color: var(--muted);
    }

    .topbar-profile {
      display: flex; align-items: center; gap: 10px;
      cursor: pointer; padding: 6px 10px; border-radius: 10px;
      transition: background 0.2s; position: relative;
    }
    .topbar-profile:hover { background: var(--bg); }
    .topbar-profile img {
      width: 34px; height: 34px; border-radius: 50%;
      object-fit: cover; border: 2px solid var(--border);
    }
    .topbar-profile-info { display: flex; flex-direction: column; }
    .topbar-profile-name { font-size: 13px; font-weight: 700; color: var(--text); line-height: 1.2; }
    .topbar-profile-role { font-size: 11px; color: var(--muted); }
    .topbar-profile-arrow { font-size: 14px; color: var(--muted); }

    .topbar-dropdown {
      position: absolute; top: calc(100% + 8px); right: 0;
      background: var(--card-bg); border: 1px solid var(--border);
      border-radius: 12px; padding: 8px; min-width: 180px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.12); z-index: 200;
      display: none;
    }
    .topbar-dropdown.show { display: block; }
    .topbar-dropdown a {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px; border-radius: 8px; text-decoration: none;
      color: var(--text); font-size: 13px; font-weight: 500;
      transition: background 0.15s;
    }
    .topbar-dropdown a:hover { background: var(--bg); }
    .topbar-dropdown a.danger { color: #dc2626; }
    .topbar-dropdown a.danger:hover { background: #fef2f2; }
    .topbar-dropdown hr { border: none; border-top: 1px solid var(--border); margin: 4px 0; }

    /* ── CONTENT ── */
    .app-content {
      flex: 1;
      padding: 24px;
      overflow-x: hidden;
    }

    /* ── DARK MODE ── */
    body.dark-mode {
      --bg: #0f172a;
      --card-bg: #1e293b;
      --text: #f1f5f9;
      --muted: #94a3b8;
      --border: #334155;
    }

    /* ── MOBILE ── */
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 99;
    }
    .sidebar-overlay.show { display: block; }

    @media (max-width: 768px) {
      .app-sidebar { transform: translateX(calc(-1 * var(--sidebar-width))); }
      .app-sidebar.mobile-open { transform: translateX(0); }
      .app-main { margin-left: 0 !important; }
      .topbar-datetime { display: none; }
    }

    /* ── CARD UTILITY ── */
    .dash-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      overflow: hidden;
    }
    .dash-card-header {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .dash-card-header h3 {
      font-size: 14px; font-weight: 700; color: var(--text); margin: 0;
    }
    .dash-card-header a {
      font-size: 12px; color: var(--primary); font-weight: 600;
      text-decoration: none; background: #eff6ff;
      padding: 4px 10px; border-radius: 6px;
    }

    /* ── STAT CARD ── */
    .stat-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      padding: 20px;
      position: relative; overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .stat-icon {
      width: 44px; height: 44px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; margin-bottom: 14px;
    }
    .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 4px; }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-sub { font-size: 11px; color: var(--muted); margin-top: 6px; }
    .stat-accent {
      position: absolute; bottom: 0; right: 0;
      width: 70px; height: 70px; border-radius: 50%;
      transform: translate(24px, 24px); opacity: 0.08;
    }

    /* ── BANNER ── */
    .dash-banner {
      border-radius: var(--radius); padding: 28px 32px;
      margin-bottom: 24px; position: relative; overflow: hidden;
      display: flex; align-items: center; justify-content: space-between;
    }
    .dash-banner::before {
      content: ''; position: absolute; top: -50px; right: 180px;
      width: 220px; height: 220px; border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .dash-banner::after {
      content: ''; position: absolute; bottom: -60px; left: 260px;
      width: 180px; height: 180px; border-radius: 50%;
      background: rgba(255,255,255,0.04);
    }
    .dash-banner h2 { color: #fff; font-size: 22px; font-weight: 800; margin: 0; }
    .dash-banner h2 span { opacity: 0.8; font-weight: 400; font-size: 18px; }
    .dash-banner p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 6px; line-height: 1.6; }
    .banner-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(255,255,255,0.18); color: #fff;
      font-size: 11px; font-weight: 700; padding: 4px 12px;
      border-radius: 20px; margin-top: 10px; letter-spacing: 0.5px;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(14px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .anim-1 { animation: fadeUp 0.4s 0.05s ease both; }
    .anim-2 { animation: fadeUp 0.4s 0.10s ease both; }
    .anim-3 { animation: fadeUp 0.4s 0.15s ease both; }
    .anim-4 { animation: fadeUp 0.4s 0.20s ease both; }
    .anim-5 { animation: fadeUp 0.4s 0.25s ease both; }
    .anim-6 { animation: fadeUp 0.4s 0.30s ease both; }
  </style>
</head>

<body>
  <div class="app-wrapper">
    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- OVERLAY mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- MAIN --}}
    <div class="app-main" id="appMain">

      {{-- TOPBAR --}}
      @include('partials.navbar')

      {{-- CONTENT --}}
      <div class="app-content">
        @yield('content')
      </div>

    </div>
  </div>

  {{-- SCRIPTS --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
  <script src="js/moment.js"></script>
  <script src="js/calendar.js"></script>

  @stack('scripts')

  {{-- FULLSCREEN IMAGE PREVIEW --}}
  <div id="fullscreenPreview" onclick="closeFullscreen()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;cursor:zoom-out;">
    <img src="" alt="preview" style="max-width:90vw;max-height:90vh;border-radius:12px;object-fit:contain;box-shadow:0 24px 80px rgba(0,0,0,0.5);">
    <div onclick="closeFullscreen()" style="position:absolute;top:20px;right:24px;color:#fff;font-size:28px;cursor:pointer;line-height:1;">✕</div>
  </div>

  <script>
    // ── DataTable init ──
    // Matikan popup error DataTables — error tetap di console tapi tidak ganggu user
    $.fn.dataTable.ext.errMode = 'none';
    function initDataTable(selector) {
      var el = document.getElementById(selector.replace('#',''));
      if (!el) return;
      if ($.fn.DataTable.isDataTable(selector)) return;

      // Hitung jumlah kolom dari thead secara otomatis
      var colCount = $(selector + ' thead tr:first th').length;
      var colDefs  = [
        { className: 'dt-head-left dt-body-left', targets: '_all' },
        { className: 'dt-body-center', targets: 0 }
      ];

      new DataTable(selector, {
        stateSave: true,
        columnDefs: colDefs,
        dom: '<"dt-length"l><"dt-search"f>rt<"dt-info"i><"dt-pagination"p>',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
          lengthMenu: "Tampilkan _MENU_ data per halaman",
          search: "Pencarian:",
        },
        initComplete: function() {
          $('.dt-custom-info').append($('.dt-info'));
          $('.dt-custom-paging').append($('.dt-paging'));
          $('.dt-layout-start').append($('.dt-search'));
          $('.dt-layout-end').append($('.dt-length'));
        }
      });
    }

    // Init semua tabel — #example untuk staff, #tabel-bimbingan/#tabel-kasus untuk siswa
    ['#example', '#tabel-bimbingan', '#tabel-kasus', '#tabel-prestasi', '#tabel-absensi'].forEach(function(id) {
      initDataTable(id);
    });

    // ── Sidebar toggle ──
    const sidebar   = document.getElementById('appSidebar');
    const main      = document.getElementById('appMain');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
          sidebar.classList.toggle('mobile-open');
          overlay.classList.toggle('show');
        } else {
          sidebar.classList.toggle('collapsed');
          main.classList.toggle('expanded');
        }
      });
    }
    if (overlay) {
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
      });
    }

    // ── Topbar profile dropdown ──
    const profileBtn = document.getElementById('topbarProfile');
    const dropdown   = document.getElementById('topbarDropdown');
    if (profileBtn && dropdown) {
      profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('show');
      });
      document.addEventListener('click', () => dropdown.classList.remove('show'));
    }

    // ── Live clock ──
    function updateClock() {
      const now  = new Date();
      const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
      const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
      const dateEl = document.getElementById('topbar-date');
      const timeEl = document.getElementById('topbar-time');
      if (dateEl) dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
      if (timeEl) timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Dark mode ──
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
      const saved = localStorage.getItem('theme');
      if (saved === 'dark') document.body.classList.add('dark-mode');
      themeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
      });
    }

    // ── Image helpers ──
    function showFullscreen(imgSrc) {
      const preview = document.getElementById('fullscreenPreview');
      if (preview) {
        preview.querySelector('img').src = imgSrc;
        preview.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    }
    function closeFullscreen() {
      const preview = document.getElementById('fullscreenPreview');
      if (preview) {
        preview.style.display = 'none';
        document.body.style.overflow = 'auto';
      }
    }
    function previewImage(event, id) {
      const reader = new FileReader();
      reader.onload = () => {
        const output = document.getElementById('profilePreview' + id);
        if (output) output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>
</html>
