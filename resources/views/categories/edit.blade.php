{{--
  Halaman: Edit Kategori (resources/views/categories/edit.blade.php)
  Ditampilkan oleh: CategoryController@edit -> return view('categories.edit', compact('category'))
  Variabel: $category (model Category yang sedang diedit, hasil Route Model Binding).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Edit Kategori')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card card-warning card-outline" style="max-width: 40rem">
  {{-- route('categories.update', $category): URL /categories/{id}; model $category otomatis diubah jadi id-nya --}}
  <form action="{{ route('categories.update', $category) }}" method="POST">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf
    {{-- @method('PUT'): HTML form hanya kenal GET/POST, ini "menyamar" sebagai PUT agar cocok dengan route update --}}
    @method('PUT')

    <div class="card-body">
      {{-- @include tanpa data array: partial otomatis ikut memakai variabel $category milik halaman ini --}}
      @include('categories._form')
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
      {{-- Tombol Batal: kembali ke daftar kategori tanpa menyimpan --}}
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
