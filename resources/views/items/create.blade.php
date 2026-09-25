{{--
  Halaman: Tambah Bahan (resources/views/items/create.blade.php)
  Ditampilkan oleh: ItemController@create -> return view('items.create', compact('categories'))
  Variabel: $categories (daftar kategori untuk dropdown di form).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Tambah Bahan')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card card-primary card-outline" style="max-width: 48rem">
  {{-- enctype multipart/form-data wajib ada agar file (foto) ikut terkirim --}}
  {{-- route('items.store'): URL POST /items -> ItemController@store --}}
  <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf

    <div class="card-body">
      {{-- @include('items._form', ['item' => null]): menyisipkan partial form; $item dikirim null karena bahan belum ada --}}
      @include('items._form', ['item' => null])
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      {{-- Tombol Batal: kembali ke daftar bahan --}}
      <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
