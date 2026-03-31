@extends('layouts.app')

@section('content')
  <div class="container min-vh-100 d-flex justify-content-center align-items-center" id="register">
    <div class="card border-0">
      <div class="card-body p-0">
        <div class="row">
          <div class="col-lg-3 d-none d-lg-flex banner flex-column justify-content-center">
            <img src="img/wavy-lines.svg" class="wavy-lines" alt="..." />
            <img src="img/12.png" class="img-fluid" width="420" alt="" />
          </div>
          <div class="col-lg-9 p-5 py-4">
            <div>
              <a href="{{ route('login') }}" class="text-decoration-none">
                <i class="uil uil-arrow-left"></i>
                <span>Kembali</span>
              </a>
              <div class="text-center mt-2">
                <a href="/" class="text-decoration-none">
                  <img src="img/app_logo_extend.png" class="img-fluid" alt="" width="120" />
                </a>
              </div>
            </div>

            <form method="POST" action="{{ route('register') }}" id="formLogin" class="mt-4">
              @csrf
              <h2>Daftar</h2>

              @if ($errors->any())
                <script>
                  document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                      icon: 'error',
                      title: 'Terdapat Kesalahan',
                      text: '{{ $errors->first() }}',
                    });
                  });
                </script>
              @endif

              {{-- NISN / NIp --}}
              <div>
                <label for="nomor_induk">NISN/NIP <span class="text-danger">*</span></label>
                <input id="nomor_induk" type="text" placeholder="Masukkan NISN atau NIP"
                  name="nomor_induk" value="{{ old('nomor_induk') }}"
                  required autocomplete="off">
              </div>

              <div class="row mt-3">
                <div class="col-lg-6">
                  <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                  <input id="name" type="text" placeholder="Nama Lengkap"
                    name="name" value="{{ old('name') }}"
                    required autocomplete="name">
                </div>
                <div class="col-lg-6">
                  <label for="email">Email <span class="text-muted small">(opsional)</span></label>
                  <input id="email" type="email" placeholder="example@gmail.com"
                    name="email" value="{{ old('email') }}"
                    autocomplete="email">
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-6">
                  <div class="wrapper">
                    <label for="passInput">Kata Sandi <span class="text-danger">*</span></label>
                    <input id="passInput" type="password" name="password"
                      placeholder="Masukkan Kata Sandi" required autocomplete="new-password">
                    <span class="eye" id="spanEye">
                      <i class="uil uil-eye-slash show-hide" toggle="#passInput" id="iconShowHide"></i>
                    </span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="wrapper">
                    <label for="password-confirm">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                    <input id="password-confirm" type="password" name="password_confirmation"
                      placeholder="Masukkan Ulang Kata Sandi" required autocomplete="new-password">
                    <span class="eye" id="spanEye2">
                      <i class="uil uil-eye-slash show-hide2" toggle="#password-confirm" id="iconShowHide2"></i>
                    </span>
                  </div>
                </div>
              </div>

              {{-- Role otomatis Siswa --}}
              <input type="hidden" name="role_id" value="1">

              <button type="submit" class="btn btn-primary w-100 mt-4" id="btnSubmit">
                <span class="text_btn">Daftar</span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
