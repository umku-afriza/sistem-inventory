@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card card-primary card-outline" style="max-width: 40rem">
  <form action="{{ route('categories.store') }}" method="POST">
    @csrf

    <div class="card-body">

      <div class="mb-3">
      <label for="name" class="form-label">Nama Kategori</label>
      <input type="text" id="name" name="name"
            class="form-control @error('name') is-invalid @enderror" />
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Deskripsi <small class="text-muted">(opsional)</small></label>
      <textarea id="description" name="description" rows="3"
                class="form-control @error('description') is-invalid @enderror"></textarea>
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
