{{--
  Halaman: Detail Bahan (resources/views/items/show.blade.php)
  Ditampilkan oleh: ItemController@show -> return view('items.show', compact('item', 'movements'))
  Variabel:
    $item      = model Item yang dibuka (kategori sudah dimuat dengan load('category'))
    $movements = riwayat transaksi stok bahan ini, di-paginate 10 per halaman
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Detail Bahan')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="row">
  {{-- Kolom kiri: kartu info bahan --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      {{-- $item->photoUrl(): method helper di model Item, mengembalikan URL foto (null jika tidak ada foto) --}}
      {{-- @if ... @endif: gambar hanya ditampilkan jika URL foto ada --}}
      @if ($item->photoUrl())
        <img src="{{ $item->photoUrl() }}" class="card-img-top" style="max-height: 16rem; object-fit: cover" alt="Foto {{ $item->name }}" />
      @endif
      <div class="card-body">
        <h4 class="mb-1">{{ $item->name }}</h4>
        <code>{{ $item->code }}</code>

        <dl class="row mt-3 mb-0">
          <dt class="col-6">Kategori</dt>
          {{-- $item->category->name: mengakses relasi belongsTo (bahan milik satu kategori) --}}
          <dd class="col-6">{{ $item->category->name }}</dd>

          <dt class="col-6">Stok</dt>
          <dd class="col-6">
            {{-- Operator ternary (kondisi ? a : b): jika isLowStock() true, teks stok diberi warna merah tebal --}}
            <span class="{{ $item->isLowStock() ? 'text-danger fw-bold' : '' }}">{{ $item->stock }} {{ $item->unit }}</span>
          </dd>

          <dt class="col-6">Stok Minimum</dt>
          <dd class="col-6">{{ $item->min_stock }} {{ $item->unit }}</dd>
        </dl>

        {{-- @if ($item->description): deskripsi hanya tampil jika diisi (tidak kosong/null) --}}
        @if ($item->description)
          <p class="text-muted mt-3 mb-0">{{ $item->description }}</p>
        @endif
      </div>
      <div class="card-footer">
        {{-- route('stock-movements.create', ['type' => ..., 'item_id' => ...]): query string ?type=in&item_id=5 agar form transaksi terisi otomatis --}}
        <a href="{{ route('stock-movements.create', ['type' => 'in', 'item_id' => $item->id]) }}" class="btn btn-success btn-sm">
          <i class="bi bi-box-arrow-in-down"></i> Masuk
        </a>
        <a href="{{ route('stock-movements.create', ['type' => 'out', 'item_id' => $item->id]) }}" class="btn btn-danger btn-sm">
          <i class="bi bi-box-arrow-up"></i> Keluar
        </a>
        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      </div>
    </div>
  </div>

  {{-- Kolom kanan: tabel riwayat stok --}}
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header"><h3 class="card-title mb-0">Riwayat Stok</h3></div>
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th class="text-end">Jumlah</th>
              <th>Keterangan</th>
              <th>Dicatat oleh</th>
            </tr>
          </thead>
          <tbody>
            {{-- @forelse ... @empty ... @endforelse: perulangan dengan bagian @empty jika belum ada riwayat --}}
            @forelse ($movements as $movement)
              <tr>
                {{-- moved_at->format('d-m-Y'): moved_at adalah objek tanggal Carbon (dari casts), format() mengubahnya jadi teks mis. 24-09-2026 --}}
                <td>{{ $movement->moved_at->format('d-m-Y') }}</td>
                {{-- $movement->type adalah enum MovementType: color() = warna badge (success/danger), label() = teks (Masuk/Keluar) --}}
                <td><span class="badge text-bg-{{ $movement->type->color() }}">{{ $movement->type->label() }}</span></td>
                <td class="text-end">{{ $movement->quantity }}</td>
                {{-- ?? '-': jika keterangan null tampilkan "-" --}}
                <td>{{ $movement->note ?? '-' }}</td>
                {{-- ?-> : aman jika admin pencatat sudah dihapus (user null) --}}
                <td>{{ $movement->user?->name ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">Belum ada riwayat.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{-- $movements->links(): tombol pagination otomatis dari Laravel (tampilan Bootstrap 5) --}}
        {{ $movements->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Tombol kembali ke daftar bahan --}}
<a href="{{ route('items.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
