{{--
  Layout auth: kerangka HTML untuk halaman login & register (tanpa header/sidebar, karena user belum login).
  Dipakai oleh auth/login.blade.php dan auth/register.blade.php lewat @extends('layouts.auth').
--}}
<!doctype html>
<html lang="id" data-bs-theme="light" data-lte-color-mode="off">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{-- config('app.name'): nama aplikasi dari .env; @yield('title'): diisi oleh @section('title') halaman anak --}}
    <title>{{ config('app.name') }} | @yield('title')</title>

    {{-- CDN: font & ikon dari internet; asset(): CSS AdminLTE dari folder public/ --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}" />
  </head>
  {{-- Layout khusus halaman login & register (tanpa sidebar) --}}
  {{-- login-page & login-box: class AdminLTE untuk kotak login di tengah layar --}}
  <body class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="login-logo">
        {{-- route('login'): membuat URL dari nama route "login", hasilnya /login --}}
        <a href="{{ route('login') }}"><b>Inventori</b> Bootcamp</a>
      </div>

      {{-- @yield('content'): tempat form login/register dari halaman anak ditampilkan --}}
      @yield('content')
    </div>

    {{-- Script Bootstrap (CDN) & AdminLTE (lokal) di akhir body --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
  </body>
</html>
