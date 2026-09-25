{{--
  Partial view (potongan view) categories._form: isi form yang sama untuk halaman tambah & edit, dipanggil dengan @include.
  Menerima variabel: $category (model Category di halaman edit, null di halaman tambah).
  $category bernilai null di halaman tambah, sehingga old() memakai string kosong.
  Nama file diawali "_" hanya kebiasaan (konvensi) untuk menandai partial, bukan halaman utuh.
--}}
{{-- Kolom Nama Kategori --}}
{{-- old('name', $category?->name): mengambil input sebelumnya jika validasi gagal; jika tidak ada, pakai nilai dari database --}}
{{-- $category?->name: operator nullsafe ?->, jika $category null (halaman tambah) hasilnya null, tidak error --}}
{{-- @error('name') is-invalid @enderror: menambahkan class merah Bootstrap "is-invalid" jika kolom ini gagal validasi --}}
<div class="mb-3">
  <label for="name" class="form-label">Nama Kategori</label>
  <input type="text" id="name" name="name" value="{{ old('name', $category?->name) }}"
         class="form-control @error('name') is-invalid @enderror" />
  {{-- @error('name') ... @enderror: blok ini hanya tampil jika ada error validasi untuk kolom "name" --}}
  {{-- $message: variabel otomatis di dalam @error, berisi teks pesan error-nya --}}
  @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Kolom Deskripsi: textarea, polanya sama (old() + @error) seperti kolom nama --}}
<div class="mb-3">
  <label for="description" class="form-label">Deskripsi <small class="text-muted">(opsional)</small></label>
  <textarea id="description" name="description" rows="3"
            class="form-control @error('description') is-invalid @enderror">{{ old('description', $category?->description) }}</textarea>
  @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
