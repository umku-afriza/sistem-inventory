{{--
  Halaman Login: form untuk masuk ke sistem.
  Ditampilkan oleh AuthController@showLogin (GET /login); form dikirim ke AuthController@login (POST /login).
  Tidak menerima variabel khusus dari controller.
--}}
{{-- @extends: memakai layout resources/views/layouts/auth.blade.php (tanpa sidebar) --}}
@extends('layouts.auth')

{{-- @section('title', ...): mengisi @yield('title') di layout dengan teks "Login" --}}
@section('title', 'Login')

{{-- @section('content') ... @endsection: isi ini dimasukkan ke @yield('content') di layout --}}
@section('content')
{{-- card card-outline card-primary: kotak (card) AdminLTE dengan garis atas berwarna biru --}}
<div class="card card-outline card-primary">
  <div class="card-body login-card-body">
    <p class="login-box-msg">Silakan login untuk masuk ke sistem</p>

    {{-- form POST ke route('login') = /login; method POST dipakai untuk mengirim data rahasia seperti password --}}
    <form action="{{ route('login') }}" method="POST">
      {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
      @csrf

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        {{-- old('email'): mengisi ulang input dengan nilai sebelumnya jika validasi/login gagal --}}
        {{-- @error('email') is-invalid @enderror: menambah class merah "is-invalid" jika field email error --}}
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" autofocus />
        {{-- @error('email'): hanya tampil jika field email gagal validasi; $message berisi pesan errornya --}}
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field password: pola sama seperti email, tapi tanpa old() (password tidak diisi ulang demi keamanan) --}}
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" />
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- checkbox "remember": jika dicentang, Laravel menyimpan cookie agar user tetap login lebih lama --}}
      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" />
        <label class="form-check-label" for="remember">Ingat saya</label>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login
      </button>
    </form>

    {{-- route('register'): link ke halaman daftar, hasilnya /register --}}
    <p class="mt-3 mb-0 text-center">
      Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
  </div>
</div>
@endsection
