{{--
  Halaman Edit Admin: form untuk mengubah data admin yang sudah ada.
  Ditampilkan oleh AdminController@edit (GET /admin/{admin}/edit); form dikirim ke AdminController@update (PUT /admin/{admin}).
  Variabel yang diterima: $admin (data admin/model User yang sedang diedit).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', ...): mengisi bagian @yield('title') di layout dengan teks "Edit Admin" --}}
@section('title', 'Edit Admin')

{{-- @section('content') ... @endsection: isi halaman, masuk ke @yield('content') di layout --}}
@section('content')
{{-- card card-warning card-outline: kotak AdminLTE dengan garis atas kuning --}}
<div class="card card-warning card-outline" style="max-width: 40rem">
  {{-- route('admin.update', $admin): URL update, contoh /admin/5 (id diambil dari $admin) --}}
  <form action="{{ route('admin.update', $admin) }}" method="POST">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf
    {{-- @method('PUT'): HTML form hanya kenal GET/POST, ini "menyamar" sebagai PUT agar cocok dengan route update --}}
    @method('PUT')

    <div class="card-body">
      <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        {{-- old('name', $admin->name): pakai input lama jika validasi gagal, jika tidak pakai data dari database --}}
        {{-- @error('name') is-invalid @enderror: menambah class "is-invalid" (kotak merah) jika field name error --}}
        <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}"
               class="form-control @error('name') is-invalid @enderror" />
        {{-- @error('name'): hanya tampil jika field name gagal validasi; $message berisi pesan errornya --}}
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field email: pola sama seperti field nama --}}
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}"
               class="form-control @error('email') is-invalid @enderror" />
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field password: opsional di halaman edit; jika dikosongkan, password lama tidak diubah --}}
      <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" />
        {{-- form-text: teks bantuan kecil berwarna abu-abu di bawah input --}}
        <div class="form-text">Kosongkan jika tidak ingin mengganti password.</div>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- password_confirmation: dicocokkan dengan field password oleh aturan validasi "confirmed" --}}
      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Ulangi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" />
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-warning">Update</button>
      {{-- tombol Batal: link kembali ke daftar admin tanpa menyimpan --}}
      <a href="{{ route('admin.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
