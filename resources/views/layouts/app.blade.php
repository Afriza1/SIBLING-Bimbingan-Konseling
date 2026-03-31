<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- SweetAlert2 - Tambahkan ini -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <title>{{ config('app.name', 'SIBLING') }} | Bimbingan Konseling</title>
  <link rel="icon" type="image/png" href="/img/app_logo.png">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

  <!-- Scripts -->
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
  {{-- @include('partials.sidebar')
  @include('partials.navbar') --}}
  <div id="app">
    <main>
      @yield('content')
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/moment.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
