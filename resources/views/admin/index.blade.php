{{--
  Halaman Daftar Admin (index): tabel semua admin + pencarian + pagination.
  Dirender oleh AdminController@index (GET /admin).
  Variabel yang diterima: $admins (data admin yang sudah di-paginate), $search (kata kunci pencarian).
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', ...): mengisi bagian @yield('title') di layout dengan teks "Manajemen Admin" --}}
@section('title', 'Manajemen Admin')

{{-- @section('content') ... @endsection: isi halaman, masuk ke @yield('content') di layout --}}
@section('content')
{{-- card: kotak Bootstrap/AdminLTE; card-header = judul, card-body = isi, card-footer = bagian bawah --}}
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar Admin</h3>
    {{-- route('admin.create'): URL ke form tambah admin, hasilnya /admin/create --}}
    <a href="{{ route('admin.create') }}" class="btn btn-primary btn-sm ms-auto">
      <i class="bi bi-plus-lg"></i> Tambah Admin
    </a>
  </div>

  <div class="card-body">
    <!-- Form pencarian -->
    {{-- Form GET: data dikirim lewat URL (query string), contoh /admin?search=budi; tidak perlu @csrf karena GET --}}
    <form action="{{ route('admin.index') }}" method="GET" class="mb-3" style="max-width: 24rem">
      <div class="input-group">
        {{-- value="{{ $search }}": kata kunci tetap tampil di kotak pencarian setelah halaman dimuat ulang --}}
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama atau email..." />
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </div>
    </form>

    {{-- table-responsive: tabel bisa digeser ke samping di layar kecil --}}
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th style="width: 3rem">No</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Terdaftar</th>
            <th style="width: 10rem">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {{-- @forelse: mengulang setiap $admin di $admins; jika kosong, bagian @empty yang ditampilkan --}}
          @forelse ($admins as $admin)
            <tr>
              {{-- Nomor urut tetap benar walau pindah halaman --}}
              {{-- $admins->firstItem(): nomor data pertama di halaman ini (misal 11 di halaman 2); $loop->index: urutan perulangan mulai 0 --}}
              <td>{{ $admins->firstItem() + $loop->index }}</td>
              <td>
                {{-- photoUrl(): method di model User yang mengembalikan URL foto profil --}}
                <img src="{{ $admin->photoUrl() }}" class="rounded-circle" style="width: 2.5rem; height: 2.5rem; object-fit: cover" alt="Foto" />
              </td>
              <td>
                {{-- {{ }}: menampilkan data PHP dan otomatis di-escape (aman dari XSS) --}}
                {{ $admin->name }}
                {{-- @if: auth()->id() = id user yang sedang login; jika sama, tampilkan badge "Anda" --}}
                @if ($admin->id === auth()->id())
                  <span class="badge text-bg-info">Anda</span>
                @endif
              </td>
              <td>{{ $admin->email }}</td>
              {{-- created_at->format('d-m-Y'): tanggal daftar diubah jadi format hari-bulan-tahun --}}
              <td>{{ $admin->created_at->format('d-m-Y') }}</td>
              <td>
                {{-- route('admin.edit', $admin): membuat URL dari nama route, contoh /admin/5/edit --}}
                <a href="{{ route('admin.edit', $admin) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>

                {{-- @if: tombol hapus tidak ditampilkan untuk akun sendiri (agar tidak menghapus diri sendiri) --}}
                @if ($admin->id !== auth()->id())
                  {{-- onsubmit="return confirm(...)": JavaScript menampilkan dialog konfirmasi; jika "Cancel", form batal dikirim --}}
                  <form action="{{ route('admin.destroy', $admin) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
                    @csrf
                    {{-- @method('DELETE'): HTML form hanya kenal GET/POST, ini "menyamar" sebagai DELETE agar cocok dengan route destroy --}}
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                      <i class="bi bi-trash"></i> Hapus
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          {{-- @empty: tampil jika $admins kosong (misal hasil pencarian tidak ada) --}}
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Data admin tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer">
    {{-- $admins->links(): menampilkan tombol pagination (1, 2, 3, Next) otomatis dari hasil paginate() --}}
    {{ $admins->links() }}
  </div>
</div>
@endsection
