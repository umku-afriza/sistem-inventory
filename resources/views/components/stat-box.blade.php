{{--
  Blade Component (anonymous): kotak statistik yang bisa dipakai berulang.
  Pemakaian: <x-stat-box color="primary" icon="bi-people-fill" :value="$total" label="Total Admin" :href="route('admin.index')" />
  "Anonymous" artinya komponen ini hanya file Blade, tanpa class PHP. Nama file stat-box.blade.php -> dipanggil <x-stat-box>.
  Dipakai di dashboard.blade.php.
--}}
{{--
  @props: daftar atribut (props) yang diterima komponen; setiap prop menjadi variabel ($color, $icon, $value, $label, $href).
  'color' => 'primary' artinya nilai default jika tidak diisi; 'icon' tanpa => berarti tidak punya default (wajib diisi).
--}}
@props(['color' => 'primary', 'icon', 'value', 'label', 'href' => null])

{{-- small-box: komponen AdminLTE berupa kotak angka berwarna; text-bg-{{ $color }} -> misal text-bg-primary (biru) --}}
<div class="small-box text-bg-{{ $color }}">
  <div class="inner">
    {{-- {{ $value }} & {{ $label }}: menampilkan nilai prop, otomatis di-escape (aman dari XSS) --}}
    <h3>{{ $value }}</h3>
    <p>{{ $label }}</p>
  </div>
  {{-- small-box-icon: ikon besar transparan di pojok kotak --}}
  <i class="small-box-icon bi {{ $icon }}"></i>
  {{-- @if ($href): link "Lihat data" hanya tampil jika prop href diisi (default null = tidak tampil) --}}
  @if ($href)
    <a href="{{ $href }}" class="small-box-footer link-light link-underline-opacity-0">
      Lihat data <i class="bi bi-arrow-right-circle"></i>
    </a>
  @endif
</div>
