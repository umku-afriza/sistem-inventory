{{--
  Partial header: navbar di bagian atas halaman. Bukan halaman sendiri, tapi potongan (partial)
  yang disisipkan ke layouts/app.blade.php lewat @include('layouts.partials.header').
--}}
<!--begin::Header-->
{{-- app-header navbar: komponen navbar Bootstrap/AdminLTE --}}
<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <!-- Tombol buka/tutup sidebar -->
    {{-- data-lte-toggle="sidebar": atribut AdminLTE, saat diklik JS AdminLTE membuka/menutup sidebar --}}
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
          {{-- <i class="bi bi-...">: ikon dari Bootstrap Icons --}}
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>

    <!-- Menu user di kanan atas -->
    {{-- ms-auto: margin kiri otomatis, mendorong menu ke kanan; dropdown: menu yang muncul saat diklik --}}
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        
        <ul class="dropdown-menu dropdown-menu-end">
          
          <li><hr class="dropdown-divider" /></li>
          <li>
            <!-- Logout wajib pakai POST agar aman -->
            {{-- Logout pakai form POST (bukan link GET) supaya tidak bisa dipicu sembarangan dari link luar --}}
            
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<!--end::Header-->
