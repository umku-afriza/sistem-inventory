{{--
  Halaman Dashboard: ringkasan data inventori setelah login.
  Dirender oleh DashboardController@index.
  Variabel yang diterima: $totalAdmin, $totalCategory, $totalItem, $totalLowStock (angka),
  $lowStockItems (daftar bahan stok menipis), $latestMovements (5 transaksi terakhir).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', ...): mengisi bagian @yield('title') di layout dengan teks "Dashboard" --}}
@section('title', 'Dashboard')

{{-- @section('content') ... @endsection: semua isi di antara keduanya masuk ke @yield('content') di layout --}}
@section('content')
{{-- row & col-lg-3 col-6: grid Bootstrap; layar besar 4 kotak per baris, layar HP 2 kotak per baris --}}
<div class="row">
  <!-- Kotak statistik, memakai Blade Component resources/views/components/stat-box.blade.php -->
  {{--
    <x-stat-box>: memanggil Blade Component. Atribut biasa (color="primary") dikirim sebagai teks/string apa adanya.
    Atribut berawalan titik dua (:value="$totalAdmin") dikirim sebagai ekspresi PHP, jadi yang dikirim isi variabelnya.
  --}}
  <div class="col-lg-3 col-6">
    <x-stat-box color="primary" icon="bi-people-fill" :value="$totalAdmin" label="Total Admin" :href="route('admin.index')" />
  </div>
  {{-- kotak-kotak berikut: pola sama, hanya beda warna, ikon, nilai, dan link --}}
  <div class="col-lg-3 col-6">
    <x-stat-box color="info" icon="bi-tags-fill" :value="$totalCategory" label="Total Kategori" :href="route('categories.index')" />
  </div>
  <div class="col-lg-3 col-6">
    <x-stat-box color="success" icon="bi-box-seam" :value="$totalItem" label="Total Bahan" :href="route('items.index')" />
  </div>
  {{-- route('items.index', ['low_stock' => 1]): array kedua jadi query string, hasilnya /items?low_stock=1 --}}
  <div class="col-lg-3 col-6">
    <x-stat-box color="danger" icon="bi-exclamation-triangle-fill" :value="$totalLowStock" label="Stok Menipis"
                :href="route('items.index', ['low_stock' => 1])" />
  </div>
</div>

{{-- card: komponen kotak dari Bootstrap/AdminLTE (card-header = judul, card-body = isi) --}}
<div class="card mb-4">
  <div class="card-body">
    {{-- auth()->user()->name: nama user yang sedang login --}}
    Selamat datang, <strong>{{ auth()->user()->name }}</strong>!
    Ini adalah sistem inventori bahan-bahan bootcamp.
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header"><h3 class="card-title mb-0">Stok Menipis</h3></div>
      <div class="card-body p-0">
        {{-- table table-striped: tabel Bootstrap dengan baris belang-belang --}}
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Bahan</th>
              <th class="text-end">Stok</th>
              <th class="text-end">Minimum</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {{-- @forelse: seperti @foreach (mengulang setiap $item), tapi punya bagian @empty jika datanya kosong --}}
            @forelse ($lowStockItems as $item)
              <tr>
                {{-- route('items.show', $item): URL detail bahan, contoh /items/5 (id diambil otomatis dari $item) --}}
                <td><a href="{{ route('items.show', $item) }}">{{ $item->name }}</a></td>
                <td class="text-end text-danger fw-bold">{{ $item->stock }} {{ $item->unit }}</td>
                <td class="text-end">{{ $item->min_stock }}</td>
                <td class="text-end">
                  {{-- tombol tambah stok: membuka form transaksi masuk dengan bahan sudah terpilih, contoh /stock-movements/create?type=in&item_id=5 --}}
                  <a href="{{ route('stock-movements.create', ['type' => 'in', 'item_id' => $item->id]) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg"></i> Stok
                  </a>
                </td>
              </tr>
            {{-- @empty: ditampilkan jika $lowStockItems kosong (tidak ada stok menipis) --}}
            @empty
              <tr><td colspan="4" class="text-center text-muted">Semua stok aman.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0">Transaksi Terakhir</h3>
        <a href="{{ route('stock-movements.index') }}" class="btn btn-link btn-sm ms-auto">Lihat semua</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Bahan</th>
              <th>Jenis</th>
              <th class="text-end">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            {{-- @forelse transaksi: pola sama seperti tabel stok menipis di atas --}}
            @forelse ($latestMovements as $movement)
              <tr>
                {{-- ->format('d-m-Y'): moved_at adalah objek tanggal (Carbon), diubah jadi teks misal 24-09-2026 --}}
                <td>{{ $movement->moved_at->format('d-m-Y') }}</td>
                {{-- $movement->item->name: mengambil nama bahan lewat relasi item (belongsTo) di model StockMovement --}}
                <td>{{ $movement->item->name }}</td>
                {{-- type adalah Enum; color() & label() = method di Enum untuk warna badge dan teks (Masuk/Keluar) --}}
                <td><span class="badge text-bg-{{ $movement->type->color() }}">{{ $movement->type->label() }}</span></td>
                <td class="text-end">{{ $movement->quantity }} {{ $movement->item->unit }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted">Belum ada transaksi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
