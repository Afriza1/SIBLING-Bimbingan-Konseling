<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/img/app_logo.png">
  <title>Menunggu Verifikasi | SIBLING</title>
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  <style>
    body { background: #f0f4f8; font-family: 'Plus Jakarta Sans', sans-serif; }
    .pending-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .pending-card {
      background: white;
      border-radius: 20px;
      padding: 48px 40px;
      max-width: 480px;
      width: 100%;
      text-align: center;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }
    .pending-icon {
      width: 80px; height: 80px;
      background: #fef9c3;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 36px;
    }
    .pending-title {
      font-size: 22px;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 12px;
    }
    .pending-desc {
      font-size: 14px;
      color: #64748b;
      line-height: 1.7;
      margin-bottom: 32px;
    }
    .pending-info {
      background: #f8fafc;
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 24px;
      text-align: left;
    }
    .pending-info .label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .pending-info .value { font-size: 14px; color: #1e293b; font-weight: 600; margin-top: 2px; }
  </style>
</head>
<body>
<div class="pending-wrapper">
  <div class="pending-card">
    <div class="pending-icon">⏳</div>
    <div class="pending-title">Akun Sedang Diverifikasi</div>
    <div class="pending-desc">
      Terima kasih sudah mendaftar! Akun kamu sedang menunggu verifikasi dari Administrator.
      Kamu akan mendapatkan akses penuh setelah Admin mengkonfirmasi dan menetapkan role yang sesuai.
    </div>

    <div class="pending-info">
      <div class="label">Nama</div>
      <div class="value">{{ auth()->user()->name }}</div>
    </div>
    <div class="pending-info">
      <div class="label">NIS / NIP</div>
      <div class="value">{{ auth()->user()->nomor_induk }}</div>
    </div>
    <div class="pending-info">
      <div class="label">Status</div>
      <div class="value" style="color:#ca8a04;">⏳ Menunggu Verifikasi Admin</div>
    </div>

    <p class="text-muted" style="font-size:13px;">Silakan hubungi Guru BK atau Admin sekolah jika membutuhkan bantuan.</p>

    <form action="{{ route('logout') }}" method="POST" class="mt-3">
      @csrf
      <button type="submit" class="btn btn-outline-secondary btn-sm">Keluar</button>
    </form>
  </div>
</div>
</body>
</html>
