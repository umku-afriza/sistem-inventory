{{--
  Halaman: Riwayat Transaksi Barang Masuk / Keluar (resources/views/stock-movements/index.blade.php)
  Ditampilkan oleh: StockMovementController@index -> return view('stock-movements.index', compact('movements', 'items', 'types'))
  Variabel:
    $movements = daftar transaksi yang sudah difilter & di-paginate (item & user ikut dimuat lewat with())
    $items     = semua bahan untuk dropdown filter
    $types     = MovementType::cases(), yaitu semua isi enum: [MovementType::In, MovementType::Out]
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Barang Masuk / Keluar')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center gap-2">
    <h3 class="card-title mb-0">Riwayat Transaksi</h3>
    {{-- route('stock-movements.create', ['type' => 'in']): query string ?type=in agar jenis "Masuk" langsung terpilih di form --}}
    <a href="{{ route('stock-movements.create', ['type' => 'in']) }}" class="btn btn-success btn-sm ms-auto">
      <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
    </a>
    <a href="{{ route('stock-movements.create', ['type' => 'out']) }}" class="btn btn-danger btn-sm">
      <i class="bi bi-box-arrow-up"></i> Barang Keluar
    </a>
  </div>

  <div class="card-body">
    {{-- form GET: filter dikirim lewat URL (?item_id=...&type=...&start_date=...), sehingga bisa di-bookmark & ikut pagination --}}
    <form action="{{ route('stock-movements.index') }}" method="GET" class="row g-2 mb-3">
      <div class="col-md-3">
        <select name="item_id" class="form-select">
          <option value="">Semua bahan</option>
          {{-- request('item_id'): membaca nilai ?item_id= dari URL --}}
          {{-- @selected(kondisi): menambahkan atribut selected jika kondisi true (bahan yang sedang difilter tetap terpilih) --}}
          @foreach ($items as $item)
            <option value="{{ $item->id }}" @selected(request('item_id') == $item->id)>{{ $item->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="type" class="form-select">
          <option value="">Semua jenis</option>
          {{-- @foreach ($types ...): mengulang isi enum; $type->value = nilai aslinya ("in"/"out"), $type->label() = teks tampilan --}}
          {{-- === : perbandingan ketat (nilai dan tipe data harus sama persis) --}}
          @foreach ($types as $type)
            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
          @endforeach
        </select>
      </div>
      {{-- input type="date": pemilih tanggal bawaan browser, nilainya berformat YYYY-MM-DD --}}
      <div class="col-md-2">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" title="Dari tanggal" />
      </div>
      <div class="col-md-2">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" title="Sampai tanggal" />
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
        {{-- Reset: link ke route tanpa query string, jadi semua filter hilang --}}
        <a href="{{ route('stock-movements.index') }}" class="btn btn-link">Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Bahan</th>
            <th>Jenis</th>
            <th class="text-end">Jumlah</th>
            <th>Keterangan</th>
            <th>Dicatat oleh</th>
          </tr>
        </thead>
        <tbody>
          {{-- @forelse ... @empty ... @endforelse: perulangan seperti @foreach, tapi punya bagian @empty jika datanya kosong --}}
          @forelse ($movements as $movement)
            <tr>
              {{-- moved_at->format('d-m-Y'): moved_at adalah objek tanggal Carbon, format() mengubahnya jadi teks mis. 24-09-2026 --}}
              <td>{{ $movement->moved_at->format('d-m-Y') }}</td>
              {{-- $movement->item: relasi belongsTo ke bahan; route('items.show', ...) membuat link ke detail bahan --}}
              <td><a href="{{ route('items.show', $movement->item) }}">{{ $movement->item->name }}</a></td>
              {{-- $movement->type adalah enum MovementType, jadi bisa memanggil method label() & color() --}}
              <td><span class="badge text-bg-{{ $movement->type->color() }}">{{ $movement->type->label() }}</span></td>
              <td class="text-end">{{ $movement->quantity }} {{ $movement->item->unit }}</td>
              {{-- ?? '-': operator null coalescing, jika keterangan null tampilkan "-" --}}
              <td>{{ $movement->note ?? '-' }}</td>
              {{-- $movement->user?->name: operator nullsafe ?->, aman jika user pencatat sudah dihapus (null) --}}
              <td>{{ $movement->user?->name ?? '-' }}</td>
            </tr>
          {{-- @empty: bagian ini tampil jika tidak ada transaksi --}}
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada transaksi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer">
    {{-- $movements->links(): tombol pagination otomatis dari Laravel (tampilan Bootstrap 5); filter ikut terbawa berkat withQueryString() --}}
    {{ $movements->links() }}
  </div>
</div>
@endsection
