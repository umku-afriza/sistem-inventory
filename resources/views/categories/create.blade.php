{{--
  Halaman: Tambah Kategori (resources/views/categories/create.blade.php)
  Ditampilkan oleh: CategoryController@create -> return view('categories.create')
  Variabel: tidak ada (form masih kosong).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi bagian @yield('title') di layout dengan teks singkat (judul halaman) --}}
@section('title', 'Tambah Kategori')

{{-- @section('content') ... @endsection: semua isi di antara ini dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card card-primary card-outline" style="max-width: 40rem">
  {{-- route('categories.store'): membuat URL dari nama route (POST /categories) -> CategoryController@store --}}
  {{-- method="POST": data form dikirim di body request, dipakai untuk menyimpan data baru --}}
  <form action="{{ route('categories.store') }}" method="POST">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf

    <div class="card-body">
      {{-- @include('categories._form', [...]): menyisipkan potongan view (partial) agar form tambah & edit tidak ditulis dua kali --}}
      {{-- ['category' => null]: mengirim data ke partial; di halaman tambah belum ada kategori, jadi null --}}
      @include('categories._form', ['category' => null])
    </div>

    <div class="card-footer">
      {{-- type="submit": tombol untuk mengirim form --}}
      <button type="submit" class="btn btn-primary">Simpan</button>
      {{-- Tombol Batal: link biasa kembali ke daftar kategori (route categories.index) --}}
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
