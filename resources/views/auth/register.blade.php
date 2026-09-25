{{--
  Halaman Register: form pendaftaran akun admin baru.
  Ditampilkan oleh AuthController@showRegister (GET /register); form dikirim ke AuthController@register (POST /register).
  Tidak menerima variabel khusus dari controller.
--}}
{{-- @extends: memakai layout resources/views/layouts/auth.blade.php (tanpa sidebar) --}}
@extends('layouts.auth')

{{-- @section('title', ...): mengisi @yield('title') di layout dengan teks "Register" --}}
@section('title', 'Register')

{{-- @section('content') ... @endsection: isi ini dimasukkan ke @yield('content') di layout --}}
@section('content')
<div class="card card-outline card-primary">
  <div class="card-body login-card-body">
    <p class="login-box-msg">Daftar akun admin baru</p>

    {{-- form POST ke route('register') = /register --}}
    <form action="{{ route('register') }}" method="POST">
      {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
      @csrf

      <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        {{-- old('name'): mengisi ulang input dengan nilai sebelumnya jika validasi gagal --}}
        {{-- @error('name') is-invalid @enderror: menambah class "is-invalid" (kotak merah) jika field name error --}}
        <input type="text" id="name" name="name" value="{{ old('name') }}"
               class="form-control @error('name') is-invalid @enderror" autofocus />
        {{-- @error('name'): hanya tampil jika field name gagal validasi; $message berisi pesan errornya --}}
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field email: pola sama seperti field nama --}}
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" />
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field password: pola sama, tanpa old() (password tidak diisi ulang demi keamanan) --}}
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" />
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- password_confirmation: nama khusus, dicocokkan dengan field password oleh aturan validasi "confirmed" --}}
      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Ulangi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" />
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-person-plus me-1"></i> Daftar
      </button>
    </form>

    {{-- route('login'): link kembali ke halaman login --}}
    <p class="mt-3 mb-0 text-center">
      Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
    </p>
  </div>
</div>
@endsection
