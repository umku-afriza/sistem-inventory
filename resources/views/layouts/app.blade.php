{{--
  Layout utama (master layout): kerangka HTML untuk semua halaman setelah login (header, sidebar, isi, footer).
  Halaman lain memakainya dengan @extends('layouts.app'), lalu mengisi bagian @yield lewat @section.
  Tema tampilan: AdminLTE 4 (template admin berbasis Bootstrap 5).
--}}
<!doctype html>
<html lang="id" data-bs-theme="light" data-lte-color-mode="off">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{-- config('app.name'): mengambil nama aplikasi dari config/app.php (nilai APP_NAME di file .env) --}}
    {{-- @yield('title'): "slot kosong" yang nanti diisi oleh @section('title', ...) di halaman anak --}}
    <title>{{ config('app.name') }} | @yield('title')</title>

    {{-- CDN (Content Delivery Network): file CSS diambil dari internet, bukan dari folder project --}}
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    {{-- asset(): membuat URL lengkap ke file di folder public/, contoh http://localhost/adminlte/css/adminlte.min.css --}}
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}" />
  </head>
  {{-- class body AdminLTE: layout-fixed = header/sidebar diam saat scroll, sidebar-expand-lg = sidebar terbuka di layar besar --}}
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    {{-- app-wrapper: pembungkus utama struktur AdminLTE (header, sidebar, main, footer) --}}
    <div class="app-wrapper">

      {{-- @include: menyisipkan file Blade lain (partial) di sini; titik "." = pemisah folder --}}
      {{-- 'layouts.partials.header' = resources/views/layouts/partials/header.blade.php --}}
      @include('layouts.partials.header')

      {{-- @include sidebar: menu navigasi di sisi kiri --}}
      @include('layouts.partials.sidebar')

      {{-- app-main: area konten utama AdminLTE (di sebelah kanan sidebar) --}}
      <!--begin::App Main-->
      <main class="app-main">
        {{-- app-content-header: judul halaman; @yield('title') dipakai lagi di sini --}}
        <div class="app-content-header">
          <div class="container-fluid">
            <h1 class="mb-0 fs-3">@yield('title')</h1>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">

            {{-- Pesan sukses / error dari controller --}}
            {{-- session('success'): flash message, dikirim controller lewat ->with('success', '...'); hanya muncul 1x setelah redirect --}}
            {{-- @if / @endif: blok ini hanya ditampilkan jika kondisinya bernilai true (ada pesan) --}}
            @if (session('success'))
              {{-- alert Bootstrap: kotak pesan hijau yang bisa ditutup dengan tombol X (data-bs-dismiss="alert") --}}
              <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            {{-- session('error'): sama seperti di atas, tapi untuk pesan gagal (kotak merah) --}}
            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            {{-- Isi halaman --}}
            {{-- @yield('content'): tempat isi @section('content') ... @endsection dari halaman anak dimasukkan --}}
            @yield('content')

          </div>
        </div>
      </main>
      <!--end::App Main-->

      {{-- @include footer: bagian bawah halaman (copyright) --}}
      @include('layouts.partials.footer')

    </div>

    {{-- Script JavaScript ditaruh di akhir body agar halaman tampil dulu sebelum script dimuat --}}
    {{-- Popper (untuk posisi dropdown) & Bootstrap JS dari CDN, lalu AdminLTE JS dari folder public/ lewat asset() --}}
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
  </body>
</html>
