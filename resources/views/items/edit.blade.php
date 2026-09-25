{{--
  Halaman: Edit Bahan (resources/views/items/edit.blade.php)
  Ditampilkan oleh: ItemController@edit -> return view('items.edit', compact('item', 'categories'))
  Variabel:
    $item       = model Item yang sedang diedit (hasil Route Model Binding)
    $categories = daftar kategori untuk dropdown
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Edit Bahan')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card card-warning card-outline" style="max-width: 48rem">
  {{-- enctype="multipart/form-data": wajib agar file foto baru ikut terkirim --}}
  {{-- route('items.update', $item): URL /items/{id}; model $item otomatis diubah jadi id-nya --}}
  <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf
    {{-- @method('PUT'): HTML form hanya kenal GET/POST, ini "menyamar" sebagai PUT agar cocok dengan route update --}}
    @method('PUT')

    <div class="card-body">
      {{-- Info stok: stok tidak diedit di sini, tapi lewat transaksi Barang Masuk / Keluar --}}
      {{-- route('stock-movements.create', ['item_id' => ...]): query string ?item_id=... agar bahan langsung terpilih di form transaksi --}}
      <div class="alert alert-info">
        Stok saat ini: <strong>{{ $item->stock }} {{ $item->unit }}</strong>.
        Untuk mengubah stok, gunakan menu <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}">Barang Masuk / Keluar</a>.
      </div>

      {{-- @include tanpa data array: partial otomatis memakai $item & $categories milik halaman ini --}}
      @include('items._form')
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
      {{-- Tombol Batal: kembali ke halaman detail bahan ini --}}
      <a href="{{ route('items.show', $item) }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
