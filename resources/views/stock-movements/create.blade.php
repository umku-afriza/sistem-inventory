{{--
  Halaman: Catat Transaksi Stok / Barang Masuk-Keluar (resources/views/stock-movements/create.blade.php)
  Ditampilkan oleh: StockMovementController@create -> return view('stock-movements.create', compact('items', 'types', 'selectedType', 'selectedItemId'))
  Variabel:
    $items          = semua bahan untuk dropdown
    $types          = MovementType::cases(), semua isi enum (In & Out)
    $selectedType   = jenis yang dipilih dari URL (?type=out), default MovementType::In
    $selectedItemId = id bahan dari URL (?item_id=3), bisa null
--}}
{{-- @extends: halaman ini memakai kerangka (layout) dari resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title', '...'): mengisi judul halaman di layout --}}
@section('title', 'Catat Transaksi Stok')

{{-- @section('content') ... @endsection: isi utama halaman yang dikirim ke @yield('content') di layout --}}
@section('content')
<div class="card card-primary card-outline" style="max-width: 40rem">
  {{-- route('stock-movements.store'): URL POST /stock-movements -> StockMovementController@store --}}
  <form action="{{ route('stock-movements.store') }}" method="POST">
    {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
    @csrf

    <div class="card-body">
      {{-- Pilihan Jenis Transaksi (radio button) --}}
      <div class="mb-3">
        <label class="form-label d-block">Jenis Transaksi</label>
        {{-- @foreach ($types ...): satu radio button untuk setiap isi enum; $type->value = "in"/"out", $type->label() = "Masuk"/"Keluar" --}}
        {{-- @checked(kondisi): menambahkan atribut checked jika kondisi true --}}
        {{-- old('type', $selectedType->value): pakai pilihan sebelumnya jika validasi gagal, jika tidak pakai jenis dari URL --}}
        @foreach ($types as $type)
          <div class="form-check form-check-inline">
            <input type="radio" id="type_{{ $type->value }}" name="type" value="{{ $type->value }}"
                   class="form-check-input @error('type') is-invalid @enderror"
                   @checked(old('type', $selectedType->value) === $type->value) />
            <label for="type_{{ $type->value }}" class="form-check-label">Barang {{ $type->label() }}</label>
          </div>
        @endforeach
        {{-- @error('type') ... @enderror: tampil hanya jika ada error validasi; $message berisi teks pesan error-nya --}}
        @error('type')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>

      {{-- Dropdown Bahan --}}
      <div class="mb-3">
        <label for="item_id" class="form-label">Bahan</label>
        {{-- @error('item_id') is-invalid @enderror: menambahkan class merah "is-invalid" jika kolom ini gagal validasi --}}
        <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror">
          <option value="">-- Pilih bahan --</option>
          {{-- @selected(kondisi): menambahkan atribut selected jika kondisi true; old('item_id', $selectedItemId) = input lama atau id dari URL --}}
          @foreach ($items as $item)
            <option value="{{ $item->id }}" @selected(old('item_id', $selectedItemId) == $item->id)>
              {{ $item->code }} - {{ $item->name }} (stok: {{ $item->stock }} {{ $item->unit }})
            </option>
          @endforeach
        </select>
        @error('item_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="row">
        {{-- Kolom Jumlah: old('quantity') = isi sebelumnya jika validasi gagal; pola @error sama seperti di atas --}}
        <div class="col-md-6 mb-3">
          <label for="quantity" class="form-label">Jumlah</label>
          <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1"
                 class="form-control @error('quantity') is-invalid @enderror" />
          @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Kolom Tanggal --}}
        {{-- today()->toDateString(): helper today() = tanggal hari ini (Carbon), toDateString() mengubahnya jadi teks YYYY-MM-DD --}}
        {{-- max="...": browser tidak mengizinkan memilih tanggal setelah hari ini --}}
        <div class="col-md-6 mb-3">
          <label for="moved_at" class="form-label">Tanggal</label>
          <input type="date" id="moved_at" name="moved_at" value="{{ old('moved_at', today()->toDateString()) }}"
                 max="{{ today()->toDateString() }}"
                 class="form-control @error('moved_at') is-invalid @enderror" />
          @error('moved_at')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      {{-- Kolom Keterangan (opsional): pola sama (old() + @error) --}}
      <div class="mb-3">
        <label for="note" class="form-label">Keterangan <small class="text-muted">(opsional)</small></label>
        <input type="text" id="note" name="note" value="{{ old('note') }}" placeholder="Contoh: dipakai kelas Laravel batch 3"
               class="form-control @error('note') is-invalid @enderror" />
        @error('note')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      {{-- Tombol Batal: kembali ke riwayat transaksi --}}
      <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
