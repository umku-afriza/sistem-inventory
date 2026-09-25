{{--
  Halaman: Daftar Kategori (resources/views/categories/index.blade.php)
  Ditampilkan oleh: CategoryController@index -> return view('categories.index', compact('categories', 'search'))
  Variabel:
    $categories = daftar kategori yang sudah di-paginate (per halaman) + kolom items_count dari withCount('items')
    $search     = kata kunci pencarian dari URL (?search=...), bisa null
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Kategori Bahan')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar Kategori</h3>
    {{-- route('categories.create'): membuat URL dari nama route -> halaman form tambah kategori --}}
    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm ms-auto">
      <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
  </div>

  <div class="card-body">
    {{-- Form pencarian --}}
    {{-- form GET: kata kunci dikirim lewat URL (?search=...), sehingga bisa di-bookmark & ikut pagination --}}
    {{-- value="{{ $search }}": isi kotak pencarian tetap tampil setelah halaman dimuat ulang --}}
    <form action="{{ route('categories.index') }}" method="GET" class="mb-3" style="max-width: 24rem">
      <div class="input-group">
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama kategori..." />
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th style="width: 3rem">No</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th style="width: 8rem">Jumlah Bahan</th>
            <th style="width: 10rem">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {{-- @forelse ... @empty ... @endforelse: perulangan seperti @foreach, tapi punya bagian @empty jika datanya kosong --}}
          @forelse ($categories as $category)
            <tr>
              {{-- Nomor urut: firstItem() = nomor data pertama di halaman ini (mis. 11 di halaman 2) --}}
              {{-- $loop->index: variabel otomatis di dalam perulangan, urutan mulai dari 0 --}}
              <td>{{ $categories->firstItem() + $loop->index }}</td>
              {{-- {{ }}: menampilkan nilai sekaligus meng-escape HTML (aman dari serangan XSS) --}}
              <td>{{ $category->name }}</td>
              {{-- ?? '-': operator null coalescing, jika deskripsi null tampilkan "-" --}}
              <td>{{ $category->description ?? '-' }}</td>
              <td>
                {{-- items_count berasal dari withCount('items') di controller --}}
                {{-- route('items.index', ['category_id' => ...]): array kedua menjadi query string -> /items?category_id=5 (daftar bahan langsung terfilter) --}}
                <a href="{{ route('items.index', ['category_id' => $category->id]) }}">{{ $category->items_count }} bahan</a>
              </td>
              <td>
                {{-- route('categories.edit', $category): URL /categories/{id}/edit --}}
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                {{-- Form hapus: hapus data harus lewat form (bukan link) agar tidak terpicu tidak sengaja --}}
                {{-- onsubmit="return confirm(...)": JavaScript menampilkan kotak konfirmasi; jika "Batal", form tidak dikirim --}}
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                  {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
                  @csrf
                  {{-- @method('DELETE'): form "menyamar" sebagai request DELETE agar cocok dengan route destroy --}}
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          {{-- @empty: bagian ini tampil jika $categories tidak berisi data --}}
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">Data kategori tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer">
    {{-- $categories->links(): tombol pagination otomatis dari Laravel (tampilan Bootstrap 5) --}}
    {{ $categories->links() }}
  </div>
</div>
@endsection
