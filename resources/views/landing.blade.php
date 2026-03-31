<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/img/app_logo.png">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <title>Bimbingan Konseling | SMKN 7 Negeri Jember</title>
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  <style>
    /* Sembunyikan label/caption carousel */
    #schoolCarousel .carousel-indicators {
      display: none;
    }
    /* Kecilkan tombol panah */
    #schoolCarousel .carousel-control-prev,
    #schoolCarousel .carousel-control-next {
      width: 32px;
      height: 32px;
      background: rgba(0,0,0,0.4);
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      opacity: 0.7;
    }
    #schoolCarousel .carousel-control-prev { left: 8px; }
    #schoolCarousel .carousel-control-next { right: 8px; }
    #schoolCarousel .carousel-control-prev-icon,
    #schoolCarousel .carousel-control-next-icon {
      width: 14px;
      height: 14px;
    }

    .school-slide {
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    }
    .school-slide img {
    width: 100%;
    height: 320px;
    object-fit: cover;
    border-radius: 16px;
    display: block;
    }
    .school-slide-placeholder {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      gap: 8px;
    }
    .school-slide-placeholder i { font-size: 52px; opacity: 0.8; }
    .school-slide-placeholder span { font-size: 15px; font-weight: 600; }
    .school-slide-placeholder small { font-size: 12px; opacity: 0.7; }
  </style>
</head>

<body>
  <header class="header">
    <nav class="navbar navbar-expand-md fixed-top" id="header">
      <div class="container">
        <button class="nav__toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <i class="uil uil-bars nav__toggler__icon"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img src="img/app_logo.png" width="48" alt="Sibling" />
          <div class="ms-2 d-flex flex-column">
            <h3>SIBLING</h3>
            <span>Bimbingan Konseling</span>
          </div>
        </a>
        <div class="offcanvas offcanvas-start offcanvas__container" data-bs-scroll="true" id="navbarNav">
          <div class="offcanvas-header offcanvas__header">
            <img src="img/app_logo.png" width="48" alt="Sibling" />
            <div class="nav__btns">
              <i class="uil uil-moon change-theme me-1" id="theme-button"></i>
              <i class="uil uil-times nav__close" data-bs-dismiss="offcanvas" aria-label="Close"></i>
            </div>
          </div>
          <div class="offcanvas-body offcanvas__body nav__menu">
            <ul class="navbar-nav ms-auto mb-lg-0">
              <li class="nav-item me-3 align-self-md-center">
                <a class="nav-link nav__link active" aria-current="page" href="#home">
                  <i class="uil uil-estate d-md-none me-2 nav__icon"></i>Home
                </a>
              </li>
              <li class="nav-item me-3 align-self-md-center">
                <a class="nav-link nav__link" href="#news">
                  <i class="uil uil-atom d-md-none me-2 nav__icon"></i>Berita
                </a>
              </li>
              <li class="nav-item me-3 align-self-md-center">
                <a class="nav-link nav__link" href="#about">
                  <i class="uil uil-pricetag-alt d-md-none me-2 nav__icon"></i>Tentang
                </a>
              </li>
              <li class="nav-item me-3 align-self-md-center">
                <a class="nav-link nav__link" href="#contact">
                  <i class="uil uil-message d-md-none me-2 nav__icon"></i>Hubungi Kami
                </a>
              </li>
            </ul>
            @guest
              <a href="{{ route('login') }}" class="btn btn-primary navbar__btn align-self-center pt-1">Login</a>
            @else
              <li class="nav-item dropdown frameProfile list-unstyled">
                <a class="nav-link dropdown-toggle nav-user" href="/#" id="navbarDropdown" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="account-user-avatar d-inline-block">
                    @if(auth()->user()->photo)
                      <img src="{{ route('user.showImage', auth()->user()->id) }}" alt="profileImg" class="cust-avatar img-fluid rounded-circle"/>
                    @else
                      <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random" class="cust-avatar img-fluid rounded-circle" style="width:48px;height:48px;object-fit:cover;"/>
                    @endif
                  </span>
                  <span class="account-user-name" id="profileName">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end me-1 border border-0 custom-rounded" aria-labelledby="navbarDropdown">
                  <li>
                    <a class="text-decoration-none" href="/home">
                      <div class="dropdown-item custom-item-dropdown d-flex align-items-center">
                        <i class="uil uil-estate me-2"></i>
                        <span class="nameItem">Home</span>
                      </div>
                    </a>
                  </li>
                </ul>
              </li>
            @endguest
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <!--==================== HOME ====================-->
    <section class="home" id="home">
    <div class="container">
        <span class="custom-badge">🎓 BK SMKN 7 Jember</span>
        <div class="row">
        <div class="col-lg-6">
            <h1 class="home__title mb-4 position-relative">Siap Kerja, Siap Sukses —<br>Kami Hadir untuk Kamu</h1>
            <p class="home__description mb-5 pe-4 position-relative">Temukan lowongan kerja terpercaya, konsultasikan karirmu,
            dan raih masa depan lebih cerah bersama Bimbingan Konseling SMKN 7 Jember.</p>
            <div class="d-flex nav__btns position-relative">
            <button class="btn button me-3" type="button" onclick="location.href='#news'">Lihat Lowongan</button>
            <button class="btn button-alt" type="button" onclick="location.href='#contact'">Hubungi BK</button>
            </div>
        </div>

        {{-- GAMBAR STATIS --}}
        <div class="col-lg-6 position-relative d-lg-inline-block d-none">
            <img src="img/ornamen1.svg" class="ornamen1" alt="" />
            <div class="ornamen mt-4 d-flex justify-content-center">
            <img src="{{ asset('img/SMKN7Jember.jpeg') }}"
                style="width:100%;height:450px;object-fit:cover;border-radius:16px;display:block;">
            </div>
        </div>
        </div>
        <img src="img/wavy-lines.svg" class="wavy-lines" alt="" />
    </div>
    </section>

    <!--==================== NEWS ====================-->
    <section class="news section" id="news">
      <div class="container">
        <h2 class="section__title">Informasi Lowongan Pekerjaan</h2>
        <p class="section__subtitle">Berita terbaru seputar dunia kerja dan karir</p>
        <div class="row mb-5">
          @foreach ($job_vacancies as $job_vacancy)
          <div class="col-lg-4 mb-4">
            <div class="card h-100">
              @if($job_vacancy->pamphlet)
                @php $ext = strtolower(pathinfo($job_vacancy->pamphlet, PATHINFO_EXTENSION)); @endphp
                @if($ext === 'pdf')
                  <div style="height:250px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;">
                    <i class="uil uil-file-pdf-alt" style="font-size:80px;color:#dc3545;"></i>
                  </div>
                @else
                  <img src="{{ asset('storage/job_vacancies/' . $job_vacancy->pamphlet) }}"
                    alt="Brosur {{ $job_vacancy->position }}"
                    class="card-img-top"
                    style="height:250px;object-fit:cover;">
                @endif
              @else
                <div style="height:250px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                  <i class="uil uil-image-slash" style="font-size:48px;color:#94a3b8;"></i>
                </div>
              @endif
              <div class="card-body">
                <h5 class="card-title">{{ $job_vacancy->position }}</h5>
                <p class="card-text text-muted small">{{ $job_vacancy->company_name }}</p>
                <p class="card-text">{{ Str::limit($job_vacancy->description, 100) }}</p>
                <button class="btn button" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $job_vacancy->id }}">Detail</button>
              </div>
            </div>
          </div>

          {{-- Modal Detail --}}
          <div class="modal fade" id="detailModal-{{ $job_vacancy->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">{{ $job_vacancy->position }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-wrap gap-3">
                  <div style="flex:1;min-width:200px;">
                    <p><strong>Posisi:</strong> {{ $job_vacancy->position }}</p>
                    <p><strong>Nama Perusahaan:</strong> {{ $job_vacancy->company_name }}</p>
                    <p><strong>Tempat:</strong> {{ $job_vacancy->location }}</p>
                    <p><strong>Gaji:</strong> {{ $job_vacancy->salary }}</p>
                    <p><strong>Dateline:</strong> {{ \Carbon\Carbon::parse($job_vacancy->dateline_date)->format('d M Y') }}</p>
                    <p><strong>Link:</strong>
                      @if($job_vacancy->link)
                        <a href="{{ $job_vacancy->link }}" target="_blank">{{ $job_vacancy->link }}</a>
                      @else
                        -
                      @endif
                    </p>
                    <p><strong>Deskripsi:</strong> {{ $job_vacancy->description }}</p>
                  </div>
                  @if($job_vacancy->pamphlet)
                    <div style="max-width:200px;flex-shrink:0;">
                      @php $ext = strtolower(pathinfo($job_vacancy->pamphlet, PATHINFO_EXTENSION)); @endphp
                      @if($ext !== 'pdf')
                        <img src="{{ asset('storage/job_vacancies/' . $job_vacancy->pamphlet) }}"
                          style="max-width:100%;height:auto;border-radius:8px;margin-bottom:8px;">
                      @endif
                      <a href="{{ route('jobVacancy.download', $job_vacancy->id) }}" class="btn btn-primary btn-sm d-block">
                        <i class="uil uil-download-alt"></i> Unduh Brosur
                      </a>
                    </div>
                  @endif
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <div class="d-flex justify-content-center">
          {{ $job_vacancies->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </section>

    <!--==================== ABOUT ====================-->
    <section class="about section" id="about">
      <div class="container">
        <h2 class="section__title">Tentang</h2>
        <p class="section__subtitle">Tentang Bimbingan Konseling SMKN 7 Negeri Jember</p>
        <div class="row">
          <div class="col-lg-6">
            <h3 class="about__title">Bimbingan Konseling SMKN 7 Negeri Jember</h3>
            <p class="about__description">Bimbingan Konseling SMKN 7 Negeri Jember merupakan sebuah layanan sekolah kepada siswa maupun guru untuk membantu mereka dalam mengatasi masalah pribadi, sosial, akademik, dan karir. Layanan ini bertujuan untuk membantu siswa agar dapat mengembangkan potensi diri, mengatasi masalah yang dihadapi, dan membuat keputusan yang tepat dalam kehidupan mereka.</p>
          </div>
          <div class="col-lg-6">
            <h3 class="about__title text-center mb-5">Dukungan</h3>
            <div class="about__logos text-center">
              <img src="img/logo-smk-7.png" class="me-2" alt="Logo 1" width="90" />
              <img src="img/smkbisa.png" class="me-2" alt="Logo 3" width="140" />
              <img src="img/app_logo_extend.png" class="me-2" alt="Logo 4" width="140" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--==================== CONTACT ====================-->
    <section class="contact section" id="contact">
      <div class="container">
        <h2 class="section__title text-center">Hubungi Kami</h2>
        <p class="section__subtitle text-center">Kami siap membantu Anda</p>
        <div class="row">
          <div class="col-lg-6">
            <div class="contact__info">
              <div class="contact__info__item d-flex mb-4">
                <i class="uil uil-phone me-3 fs-4"></i>
                <div><h3>Telepon</h3><span>031-8292038</span></div>
              </div>
              <div class="contact__info__item d-flex mb-4">
                <i class="uil uil-envelope me-3 fs-4"></i>
                <div><h3>Email</h3><span><a href="mailto:info@smkn7jember.sch.id" class="contact__info__email">info@smkn7jember.sch.id</a></span></div>
              </div>
              <div class="contact__info__item d-flex mb-4">
                <i class="uil uil-map-marker me-3 fs-4"></i>
                <div><h3>Alamat</h3><span>Jl. Randu Agung Jatiroto, Jam Koong, Jatiroto, Kec. Sumberbaru, Kabupaten Jember, Jawa Timur 68156</span></div>
              </div>
              <div class="contact__info__item d-flex">
                <i class="uil uil-clock me-3 fs-4"></i>
                <div><h3>Jam Kerja</h3><span>Senin - Jumat: 07.00 - 15.00</span></div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mt-5 mt-lg-0">
            <form action="{{ route('submit.form') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama anda" required />
              </div>
              <div class="form-group mt-3">
                <label class="form-label">Nomor WhatsApp</label>
                <input type="number" name="phone_number" class="form-control" placeholder="Masukkan nomor WhatsApp anda" required />
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mt-3">
                    <label class="form-label">Pilih Tanggal</label>
                    <input type="date" name="booking_date" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mt-3">
                    <label class="form-label">Pilih Waktu</label>
                    <select name="booking_time" class="form-control @error('booking_time') is-invalid @enderror" required>
                      <option value="" disabled selected>Pilih Waktu</option>
                      @php
                        $timeSlots = ['09:00', '09:15', '11:30', '11:45'];
                        $maxBookingPerSlot = 3;
                        foreach ($timeSlots as $time) {
                          $bookedCount = \App\Models\GuidanceBooking::where('booking_date', now()->format('Y-m-d') . " $time:00")->count();
                          $remainingSlots = $maxBookingPerSlot - $bookedCount;
                          $disabled = $remainingSlots <= 0 ? 'disabled' : '';
                          echo "<option value='$time' $disabled>$time - Sisa $remainingSlots orang</option>";
                        }
                      @endphp
                    </select>
                    @error('booking_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>
              <input type="hidden" name="status" value="pending">
              <button type="submit" class="btn btn-primary mt-4">Kirim Pesan</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!--==================== Footer ====================-->
  <footer class="footer py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <img src="img/app_logo_extend_w.png" alt="" height="50" />
          <p class="mt-4 pe-lg-5">Aplikasi Bimbingan Konseling Digital di SMKN 7 Jember adalah platform yang dirancang untuk membantu siswa dalam mengatasi masalah pribadi, sosial, akademik, dan karir. Dengan aplikasi ini, siswa dapat dengan mudah mengakses layanan bimbingan konseling secara online, membuat janji temu dengan konselor, dan mendapatkan berbagai sumber daya yang berguna untuk pengembangan diri.</p>
          <ul class="social-list list-inline mt-3">
            <li class="list-inline-item text-center"><a href="#" class="social-list-item border-primary text-primary"><i class="bx bxl-facebook"></i></a></li>
            <li class="list-inline-item text-center"><a href="#" class="social-list-item border-danger text-danger"><i class="bx bxl-google"></i></a></li>
            <li class="list-inline-item text-center"><a href="#" class="social-list-item border-info text-info"><i class="bx bxl-twitter"></i></a></li>
            <li class="list-inline-item text-center"><a href="#" class="social-list-item border-secondary text-secondary"><i class="bx bxl-linkedin"></i></a></li>
          </ul>
        </div>
        <div class="col-lg-auto mt-3 mt-lg-0 ms-auto">
          <h5 class="mb-3">Link Terkait</h5>
          <ul class="nav flex-column">
            <li class="nav-item"><a href="https://smkn7jember.sch.id/" class="nav-link px-0 py-1">Profil SMKN 7 Jember</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">Berita Karir</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">BKK</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">LSP</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">Pengumuman</a></li>
          </ul>
        </div>
        <div class="col-lg-auto mt-3 mt-lg-0 ms-auto">
          <h5 class="mb-3">Bantuan</h5>
          <ul class="nav flex-column">
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">Kebijakan Privasi</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">Syarat dan Ketentuan</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-0 py-1">Hubungi Kami</a></li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <p class="mt-5 text-center mb-0">©2024 SIBLING | SMKN 7 Jember</p>
        </div>
      </div>
    </div>
  </footer>

  <a href="#home" class="scrollup" id="scroll-up">
    <i class="uil uil-arrow-up scrollup__icon"></i>
  </a>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
  <script src="js/main.js"></script>
  <script>
    $(document).ready(function() {
      // Fix jQuery error: cek target ada sebelum scroll
      $('a[href^="#"]').on('click', function(e) {
        var href = $(this).attr('href');
        if (!href || href === '#' || href.length <= 1) return;
        e.preventDefault();
        var targetId = href.substring(1);
        var targetElement = $('#' + CSS.escape(targetId));
        if (targetElement.length) {
          $('html, body').animate({ scrollTop: targetElement.offset().top }, 100);
          history.pushState(null, null, ' ');
        }
      });
    });
  </script>
</body>
</html>
