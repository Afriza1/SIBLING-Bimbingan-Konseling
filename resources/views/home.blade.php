@extends('layouts.dashboard')

@section('content')
  <div>
    <div class="content">
      <div class="row pt-4">
        <div class="mb-4">
          <div class="p-0">
            <div class="row mb-2">
              <div class="col-lg-8 m-0">
                <div class="custCard mb-4" style="padding: 20px">
                  <img src="img/footer-bg.png" class="ornament" alt="">
                  <div class="order-2 position-relative z-10">
                    <img src="img/cardChar.png" width="200" alt="">
                  </div>
                  <div class="mt-2 order-1 flex-fill align-content-center">
                    <h3>
                      Selamat Pagi, <span style="font-family: NunitoSans-ExtraBold">{{ auth()->user()->name }}</span>
                    </h3>
                    <p class="mt-2 m-0" style="line-height: 1.625;">Semoga harimu menyenangkan di tempat kerja</p>
                    <p>Kelola data sesi bimbingan Anda!</p>

                    <button class="btn mt-3 text-white">
                      Lihat data
                    </button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card" id="data1">
                      <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                          <div class="card-icon text-white">
                            <i class="uil uil-users-alt"></i>
                          </div>
                          <div class=" ms-auto card-detail">
                            <p class="mb-0 card-detail_text">Data Bimbingan</p>
                            <h4 class="my-1 card-detail_data">{{ $total_guidances }}</h4>
                          </div>
                        </div>
                      </div>
                      <div class="abstract1"></div>
                      <div class="abstract2"></div>
                    </div>
                  </div>
                  <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card" id="data2">
                      <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                          <div class="card-icon text-white">
                            <i class="uil uil-database"></i>
                          </div>
                          <div class=" ms-auto card-detail">
                            <p class="mb-0 card-detail_text">Data Kasus</p>
                            <h4 class="my-1 card-detail_data">{{ $total_cases }}</h4>
                          </div>
                        </div>
                      </div>
                      <div class="abstract1"></div>
                      <div class="abstract2"></div>
                    </div>
                  </div>
                  <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card" id="data3">
                      <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                          <div class="card-icon text-white">
                            <i class="uil uil-notes"></i>
                          </div>
                          <div class=" ms-auto card-detail">
                            <p class="mb-0 card-detail_text">Data Loker</p>
                            <h4 class="my-1 card-detail_data">{{ $total_job_vacancies }}</h4>
                          </div>
                        </div>
                      </div>
                      <div class="abstract1"></div>
                      <div class="abstract2"></div>
                    </div>
                  </div>
                </div>
                <div class="container">
                  <div class="row">
                    <!-- Grafik Bimbingan -->
                    <div class="col-lg-6 mb-4">
                      <div class="card">
                        <div class="card-header">
                          <h5>Grafik Bimbingan per Hari (Bulan Ini)</h5>
                        </div>
                        <div class="card-body">
                          <canvas id="dailyGuidancesChart" width="400" height="200"></canvas>
                        </div>
                      </div>
                    </div>

                    <!-- Grafik Karir -->
                    <div class="col-lg-6 mb-4">
                      <div class="card">
                        <div class="card-header">
                          <h5>Grafik Karir per Bulan (Tahun Ini)</h5>
                        </div>
                        <div class="card-body">
                          <canvas id="monthlyCareersChart" width="400" height="200"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Grafik Kasus -->
                    <div class="col-lg-6 mb-4">
                      <div class="card">
                        <div class="card-header">
                          <h5>Grafik Kasus per Hari (Bulan Ini)</h5>
                        </div>
                        <div class="card-body">
                          <canvas id="dailyCasesChart" width="400" height="200"></canvas>
                        </div>
                      </div>
                    </div>

                    <!-- Grafik Prestasi -->
                    <div class="col-lg-6 mb-4">
                      <div class="card">
                        <div class="card-header">
                          <h5>Grafik Prestasi per Bulan (Tahun Ini)</h5>
                        </div>
                        <div class="card-body">
                          <canvas id="monthlyAchievementsChart" width="400" height="200"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Grafik Absensi -->
                    <div class="col-lg-12 mb-4">
                      <div class="card">
                        <div class="card-header">
                          <h5>Grafik Absensi per Hari (Bulan Ini)</h5>
                        </div>
                        <div class="card-body">
                          <canvas id="dailyAttendancesChart" width="400" height="200"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                  // Grafik Bimbingan
                  var ctxGuidances = document.getElementById('dailyGuidancesChart').getContext('2d');
                  var dailyGuidancesChart = new Chart(ctxGuidances, {
                    type: 'line',
                    data: {
                      labels: @json(range(1, $days_in_month)),
                      datasets: [{
                        label: 'Jumlah Bimbingan per Hari',
                        data: @json($guidances_per_day),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.4
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        x: {
                          title: {
                            display: true,
                            text: 'Hari'
                          }
                        },
                        y: {
                          title: {
                            display: true,
                            text: 'Jumlah Bimbingan'
                          },
                          beginAtZero: true
                        }
                      }
                    }
                  });

                  // Grafik Kasus
                  var ctxCases = document.getElementById('dailyCasesChart').getContext('2d');
                  var dailyCasesChart = new Chart(ctxCases, {
                    type: 'line',
                    data: {
                      labels: @json(range(1, $days_in_month)),
                      datasets: [{
                        label: 'Jumlah Kasus per Hari',
                        data: @json($cases_per_day),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        x: {
                          title: {
                            display: true,
                            text: 'Hari'
                          }
                        },
                        y: {
                          title: {
                            display: true,
                            text: 'Jumlah Kasus'
                          },
                          beginAtZero: true
                        }
                      }
                    }
                  });

                  // Grafik Absensi
                  var ctxAttendances = document.getElementById('dailyAttendancesChart').getContext('2d');
                  var dailyAttendancesChart = new Chart(ctxAttendances, {
                    type: 'line',
                    data: {
                      labels: @json(range(1, $days_in_month)),
                      datasets: [{
                        label: 'Jumlah Absensi per Hari',
                        data: @json($attendances_per_day),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        x: {
                          title: {
                            display: true,
                            text: 'Hari'
                          }
                        },
                        y: {
                          title: {
                            display: true,
                            text: 'Jumlah Absensi'
                          },
                          beginAtZero: true
                        }
                      }
                    }
                  });

                  // Grafik Karir
                  var ctxCareers = document.getElementById('monthlyCareersChart').getContext('2d');
                  var monthlyCareersChart = new Chart(ctxCareers, {
                    type: 'bar',
                    data: {
                      labels: @json($months_in_year),  // Menggunakan angka bulan
                      datasets: [{
                        label: 'Jumlah Karir per Bulan',
                        data: @json($careers_per_month),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        x: {
                          title: {
                            display: true,
                            text: 'Bulan'
                          }
                        },
                        y: {
                          title: {
                            display: true,
                            text: 'Jumlah Karir'
                          },
                          beginAtZero: true
                        }
                      }
                    }
                  });

                  // Grafik Prestasi
                  var ctxAchievements = document.getElementById('monthlyAchievementsChart').getContext('2d');
                  var monthlyAchievementsChart = new Chart(ctxAchievements, {
                    type: 'bar',
                    data: {
                      labels: @json($months_in_year),  // Menggunakan angka bulan
                      datasets: [{
                        label: 'Jumlah Prestasi per Bulan',
                        data: @json($achievements_per_month),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        x: {
                          title: {
                            display: true,
                            text: 'Bulan'
                          }
                        },
                        y: {
                          title: {
                            display: true,
                            text: 'Jumlah Prestasi'
                          },
                          beginAtZero: true
                        }
                      }
                    }
                  });
                </script>
              </div>
              <div class="col-lg-4 m-0">
                @include('partials.calender')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row gx-4 pt-4">
    <div class="col-lg-9">
    </div>
    <div class="col-lg-3 m-0">
    </div>
  </div>
  </div>
@endsection
