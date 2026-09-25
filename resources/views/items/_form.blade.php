{{--
  Partial view (potongan view) items._form: isi form yang sama untuk halaman tambah & edit bahan.
  Menerima variabel:
    $item       = model Item di halaman edit, null di halaman tambah
    $categories = daftar kategori untuk pilihan dropdown
  $item bernilai null di halaman tambah.
--}}
<div class="row">
  {{-- Kolom Kode Bahan --}}
  {{-- old('code', $item?->code): pakai input sebelumnya jika validasi gagal; jika tidak ada, pakai nilai dari database --}}
  {{-- $item?->code: operator nullsafe ?->, jika $item null (halaman tambah) hasilnya null, tidak error --}}
  {{-- @error('code') is-invalid @enderror: menambahkan class merah "is-invalid" jika kolom ini gagal validasi --}}
  <div class="col-md-4 mb-3">
    <label for="code" class="form-label">Kode Bahan</label>
    <input type="text" id="code" name="code" value="{{ old('code', $item?->code) }}" placeholder="BHN-001"
           class="form-control @error('code') is-invalid @enderror" />
    {{-- @error ... @enderror: tampil hanya jika ada error validasi; $message berisi teks pesan error-nya --}}
    @error('code')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Kolom Nama Bahan: pola sama (old() + @error) --}}
  <div class="col-md-8 mb-3">
    <label for="name" class="form-label">Nama Bahan</label>
    <input type="text" id="name" name="name" value="{{ old('name', $item?->name) }}"
           class="form-control @error('name') is-invalid @enderror" />
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row">
  {{-- Dropdown Kategori (select) --}}
  <div class="col-md-8 mb-3">
    <label for="category_id" class="form-label">Kategori</label>
    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
      <option value="">-- Pilih kategori --</option>
      {{-- @foreach: mengulang setiap kategori menjadi satu <option> --}}
      {{-- @selected(kondisi): menambahkan atribut selected jika kondisi true (kategori lama/tersimpan terpilih otomatis) --}}
      @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(old('category_id', $item?->category_id) == $category->id)>
          {{ $category->name }}
        </option>
      @endforeach
    </select>
    @error('category_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    {{-- @if ... @endif: kondisi; isEmpty() = method collection, true jika belum ada kategori sama sekali --}}
    @if ($categories->isEmpty())
      <div class="form-text">Belum ada kategori. <a href="{{ route('categories.create') }}">Tambah kategori</a> terlebih dahulu.</div>
    @endif
  </div>

  {{-- Kolom Satuan: pola sama (old() + @error) --}}
  <div class="col-md-4 mb-3">
    <label for="unit" class="form-label">Satuan</label>
    <input type="text" id="unit" name="unit" value="{{ old('unit', $item?->unit) }}" placeholder="pcs, box, rim..."
           class="form-control @error('unit') is-invalid @enderror" />
    @error('unit')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row">
  {{-- Stok awal hanya bisa diisi saat tambah. Setelah itu stok diubah lewat menu Transaksi. --}}
  {{-- @if (! $item): tanda ! artinya "tidak"; true jika $item null, yaitu di halaman tambah --}}
  @if (! $item)
    <div class="col-md-6 mb-3">
      <label for="stock" class="form-label">Stok Awal</label>
      {{-- old('stock', 0): nilai bawaan (default) 0 jika belum pernah diisi --}}
      <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
             class="form-control @error('stock') is-invalid @enderror" />
      @error('stock')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  @endif

  {{-- Kolom Stok Minimum --}}
  {{-- $item?->min_stock ?? 0: operator null coalescing ??, jika hasilnya null (halaman tambah) pakai 0 --}}
  <div class="col-md-6 mb-3">
    <label for="min_stock" class="form-label">Stok Minimum</label>
    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $item?->min_stock ?? 0) }}" min="0"
           class="form-control @error('min_stock') is-invalid @enderror" />
    <div class="form-text">Bahan ditandai "menipis" jika stok &le; angka ini.</div>
    @error('min_stock')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

{{-- Upload Foto --}}
<div class="mb-3">
  <label for="photo" class="form-label">Foto <small class="text-muted">(opsional, jpg/png maks 2 MB)</small></label>
  {{-- @if ($item?->photo): tampilkan foto lama hanya jika sedang edit dan bahan sudah punya foto --}}
  @if ($item?->photo)
    <div class="mb-2">
      {{-- $item->photoUrl(): method helper di model Item, mengubah path file foto menjadi URL yang bisa dibuka browser --}}
      <img src="{{ $item->photoUrl() }}" class="rounded" style="max-height: 6rem" alt="Foto bahan" />
    </div>
  @endif
  {{-- input type="file": kolom pilih file; accept membatasi pilihan ke gambar png/jpeg --}}
  {{-- File hanya ikut terkirim jika <form> punya enctype="multipart/form-data" (ada di create & edit) --}}
  <input type="file" id="photo" name="photo" accept="image/png, image/jpeg"
         class="form-control @error('photo') is-invalid @enderror" />
  @error('photo')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Kolom Deskripsi: textarea, pola sama (old() + @error) --}}
<div class="mb-3">
  <label for="description" class="form-label">Deskripsi <small class="text-muted">(opsional)</small></label>
  <textarea id="description" name="description" rows="3"
            class="form-control @error('description') is-invalid @enderror">{{ old('description', $item?->description) }}</textarea>
  @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
