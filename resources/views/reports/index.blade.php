{{--
  Halaman: Laporan Stok (resources/views/reports/index.blade.php)
  Ditampilkan oleh: ReportController@index -> return view('reports.index', compact('items', 'startDate', 'endDate'))
  Variabel:
    $items     = Collection semua bahan + kolom total_in & total_out (hasil withSum dalam periode)
    $startDate = tanggal awal periode (objek Carbon), default awal bulan ini
    $endDate   = tanggal akhir periode (objek Carbon), default akhir bulan ini
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Laporan Stok')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card">
  <div class="card-header">
    {{-- form GET: periode dikirim lewat URL (?start_date=...&end_date=...), sehingga laporan bisa di-bookmark --}}
    <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label for="start_date" class="form-label">Dari tanggal</label>
        {{-- $startDate->toDateString(): mengubah objek Carbon jadi teks YYYY-MM-DD, format yang dibutuhkan input type="date" --}}
        {{-- @error('start_date') is-invalid @enderror: kotak jadi merah jika tanggal tidak valid --}}
        <input type="date" id="start_date" name="start_date" value="{{ $startDate->toDateString() }}"
               class="form-control @error('start_date') is-invalid @enderror" />
      </div>
      <div class="col-md-3">
        <label for="end_date" class="form-label">Sampai tanggal</label>
        <input type="date" id="end_date" name="end_date" value="{{ $endDate->toDateString() }}"
               class="form-control @error('end_date') is-invalid @enderror" />
      </div>
      <div class="col-md-6">
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button>
        {{-- request()->query() meneruskan filter tanggal yang sedang dipakai ke link export --}}
        {{-- route('reports.export', [...]): array query string ditempel ke URL -> /reports/export?start_date=...&end_date=... --}}
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success">
          <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
      </div>
      {{-- @error('end_date') ... @enderror: tampil jika validasi gagal (mis. tanggal akhir sebelum tanggal awal); $message = teks error --}}
      @error('end_date')
        <div class="col-12 text-danger small">{{ $message }}</div>
      @enderror
    </form>
  </div>

  <div class="card-body">
    {{-- translatedFormat('d F Y'): format tanggal Carbon dengan nama bulan sesuai bahasa aplikasi, mis. "24 September 2026" --}}
    <p class="text-muted">
      Periode: {{ $startDate->translatedFormat('d F Y') }} &ndash; {{ $endDate->translatedFormat('d F Y') }}
    </p>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Kategori</th>
            <th class="text-end">Masuk</th>
            <th class="text-end">Keluar</th>
            <th class="text-end">Stok Saat Ini</th>
          </tr>
        </thead>
        <tbody>
          {{-- @forelse ... @empty ... @endforelse: perulangan seperti @foreach, tapi punya bagian @empty jika datanya kosong --}}
          @forelse ($items as $item)
            <tr>
              <td><code>{{ $item->code }}</code></td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->category->name }}</td>
              {{-- total_in & total_out berasal dari withSum() di ReportController --}}
              {{-- ?? 0: jika tidak ada transaksi dalam periode, SUM menghasilkan null, maka tampilkan 0 --}}
              <td class="text-end text-success">{{ $item->total_in ?? 0 }}</td>
              <td class="text-end text-danger">{{ $item->total_out ?? 0 }}</td>
              <td class="text-end">{{ $item->stock }} {{ $item->unit }}</td>
            </tr>
          {{-- @empty: bagian ini tampil jika belum ada bahan sama sekali --}}
          @empty
            <tr><td colspan="6" class="text-center text-muted">Belum ada data bahan.</td></tr>
          @endforelse
        </tbody>
        {{-- @if ($items->isNotEmpty()): baris total (tfoot) hanya ditampilkan jika collection berisi data --}}
        @if ($items->isNotEmpty())
          <tfoot class="fw-bold">
            <tr>
              <td colspan="3">Total</td>
              {{-- Method collection sum() menjumlahkan nilai di PHP, bukan di database --}}
              <td class="text-end">{{ $items->sum('total_in') }}</td>
              <td class="text-end">{{ $items->sum('total_out') }}</td>
              <td></td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>
@endsection
