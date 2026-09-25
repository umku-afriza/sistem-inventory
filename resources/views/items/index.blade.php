{{--
  Halaman: Daftar Bahan (resources/views/items/index.blade.php)
  Ditampilkan oleh: ItemController@index -> return view('items.index', compact('items', 'categories'))
  Variabel:
    $items      = daftar bahan yang sudah difilter & di-paginate, kategori ikut dimuat lewat with('category')
    $categories = semua kategori untuk dropdown filter
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Data Bahan')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar Bahan</h3>
    {{-- route('items.create'): membuat URL dari nama route -> halaman form tambah bahan --}}
    <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm ms-auto">
      <i class="bi bi-plus-lg"></i> Tambah Bahan
    </a>
  </div>

  <div class="card-body">
    {{-- Form filter: dikirim lewat GET sehingga filter tersimpan di URL --}}
    {{-- form GET: filter dikirim lewat URL (?search=...&category_id=...), sehingga bisa di-bookmark & ikut pagination --}}
    <form action="{{ route('items.index') }}" method="GET" class="row g-2 mb-3">
      <div class="col-md-4">
        {{-- request('search'): helper untuk membaca nilai ?search= dari URL, agar isian filter tetap tampil --}}
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau kode..." />
      </div>
      <div class="col-md-3">
        <select name="category_id" class="form-select">
          <option value="">Semua kategori</option>
          {{-- @foreach: satu <option> untuk setiap kategori --}}
          {{-- @selected(kondisi): menambahkan atribut selected jika kondisi true (kategori yang sedang difilter tetap terpilih) --}}
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-center">
        <div class="form-check">
          {{-- request()->boolean('low_stock'): membaca ?low_stock= sebagai true/false ("1", "on", "true" = true) --}}
          {{-- @checked(kondisi): menambahkan atribut checked pada checkbox jika kondisi true --}}
          <input type="checkbox" name="low_stock" value="1" id="low_stock" class="form-check-input" @checked(request()->boolean('low_stock')) />
          <label for="low_stock" class="form-check-label">Stok menipis</label>
        </div>
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
        {{-- Reset: link ke route tanpa query string, jadi semua filter hilang --}}
        <a href="{{ route('items.index') }}" class="btn btn-link">Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th style="width: 3rem">No</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th class="text-end">Stok</th>
            <th style="width: 14rem">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {{-- @forelse ... @empty ... @endforelse: perulangan seperti @foreach, tapi punya bagian @empty jika datanya kosong --}}
          @forelse ($items as $item)
            <tr>
              {{-- Nomor urut: firstItem() = nomor data pertama di halaman ini; $loop->index = urutan dalam loop, mulai 0 --}}
              <td>{{ $items->firstItem() + $loop->index }}</td>
              <td><code>{{ $item->code }}</code></td>
              <td>{{ $item->name }}</td>
              {{-- $item->category tersedia tanpa query tambahan berkat with('category') --}}
              <td>{{ $item->category->name }}</td>
              <td class="text-end">
                {{ $item->stock }} {{ $item->unit }}
                {{-- $item->isLowStock(): method helper di model Item, true jika stok <= stok minimum -> tampilkan badge "Menipis" --}}
                @if ($item->isLowStock())
                  <span class="badge text-bg-danger">Menipis</span>
                @endif
              </td>
              <td>
                {{-- route('items.show', $item): URL /items/{id} -> halaman detail bahan --}}
                <a href="{{ route('items.show', $item) }}" class="btn btn-info btn-sm">
                  <i class="bi bi-eye"></i> Detail
                </a>
                <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                {{-- Form hapus: onsubmit="return confirm(...)" menampilkan kotak konfirmasi; jika "Batal", form tidak dikirim --}}
                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus bahan ini? Riwayat stoknya juga akan terhapus.')">
                  {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
                  @csrf
                  {{-- @method('DELETE'): form "menyamar" sebagai request DELETE agar cocok dengan route destroy --}}
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          {{-- @empty: bagian ini tampil jika tidak ada bahan yang cocok --}}
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Data bahan tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer">
    {{-- $items->links(): tombol pagination otomatis dari Laravel (tampilan Bootstrap 5) --}}
    {{ $items->links() }}
  </div>
</div>
@endsection
