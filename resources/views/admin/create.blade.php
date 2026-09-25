{{--
  Halaman Tambah Admin (create): form untuk membuat admin baru.
  Ditampilkan oleh AdminController@create (GET /admin/create); form dikirim ke AdminController@store (POST /admin).
  Tidak menerima variabel khusus dari controller.
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', ...): mengisi bagian @yield('title') di layout dengan teks "Tambah Admin" --}}
@section('title', 'Tambah Admin')

{{-- @section('content') ... @endsection: isi halaman, masuk ke @yield('content') di layout --}}
@section('content')
{{-- card card-primary card-outline: kotak AdminLTE dengan garis atas biru --}}
<div class="card card-primary card-outline" style="max-width: 40rem">
  {{-- route('admin.store'): URL untuk menyimpan data baru, hasilnya /admin (method POST) --}}
  <form action="{{ route('admin.store') }}" method="POST">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf

    <div class="card-body">
      <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        {{-- old('name'): mengisi ulang input dengan nilai sebelumnya jika validasi gagal --}}
        {{-- @error('name') is-invalid @enderror: menambah class "is-invalid" (kotak merah) jika field name error --}}
        <input type="text" id="name" name="name" value="{{ old('name') }}"
               class="form-control @error('name') is-invalid @enderror" />
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

      {{-- password_confirmation: dicocokkan dengan field password oleh aturan validasi "confirmed" --}}
      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Ulangi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" />
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      {{-- tombol Batal: link biasa kembali ke daftar admin (tidak mengirim form) --}}
      <a href="{{ route('admin.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
