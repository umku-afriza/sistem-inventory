{{--
  Halaman Update Profile: user yang sedang login mengubah nama, email, password, dan foto profilnya sendiri.
  Ditampilkan oleh ProfileController@edit (GET /profile); form dikirim ke ProfileController@update (PUT /profile).
  Variabel yang diterima: $user (user yang sedang login).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', ...): mengisi bagian @yield('title') di layout dengan teks "Update Profile" --}}
@section('title', 'Update Profile')

{{-- @section('content') ... @endsection: isi halaman, masuk ke @yield('content') di layout --}}
@section('content')
<div class="card card-primary card-outline" style="max-width: 40rem">
  {{-- enctype wajib ada agar form bisa mengirim file (foto) --}}
  {{-- enctype="multipart/form-data": tanpa ini, input type="file" tidak ikut terkirim ke server --}}
  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf
    {{-- @method('PUT'): HTML form hanya kenal GET/POST, ini "menyamar" sebagai PUT agar cocok dengan route profile.update --}}
    @method('PUT')

    <div class="card-body">
      {{-- Foto profil saat ini; photoUrl() = method di model User yang mengembalikan URL foto (atau gambar default) --}}
      <div class="text-center mb-3">
        <img src="{{ $user->photoUrl() }}" class="rounded-circle shadow"
             style="width: 7rem; height: 7rem; object-fit: cover" alt="Foto profil" />
      </div>

      <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        {{-- old('name', $user->name): pakai input lama jika validasi gagal, jika tidak pakai data dari database --}}
        {{-- @error('name') is-invalid @enderror: menambah class "is-invalid" (kotak merah) jika field name error --}}
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror" />
        {{-- @error('name'): hanya tampil jika field name gagal validasi; $message berisi pesan errornya --}}
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field email: pola sama seperti field nama --}}
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
               class="form-control @error('email') is-invalid @enderror" />
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- field password: opsional; jika dikosongkan, password lama tidak diubah --}}
      <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" />
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

      {{-- input type="file": untuk upload foto; accept="image/*" membatasi pilihan file hanya gambar di dialog browser --}}
      <div class="mb-3">
        <label for="photo" class="form-label">Foto Profil</label>
        <input type="file" id="photo" name="photo" accept="image/*"
               class="form-control @error('photo') is-invalid @enderror" />
        <div class="form-text">Format JPG/PNG, maksimal 2 MB.</div>
        {{-- @error('photo'): pesan error jika file bukan gambar atau terlalu besar --}}
        @error('photo')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection
