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
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
          {{-- auth()->user(): mengambil data user yang sedang login (model User); photoUrl() = method di model User untuk URL foto --}}
          <img src="{{ auth()->user()->photoUrl() }}" class="rounded-circle shadow me-2"
               style="width: 2rem; height: 2rem; object-fit: cover" alt="Foto profil" />
          {{-- {{ }}: menampilkan data PHP (nama user) dan otomatis di-escape agar aman dari XSS --}}
          <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            {{-- route('profile.edit'): URL dari nama route, hasilnya /profile --}}
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <i class="bi bi-person-gear me-2"></i> Update Profile
            </a>
          </li>
          <li><hr class="dropdown-divider" /></li>
          <li>
            <!-- Logout wajib pakai POST agar aman -->
            {{-- Logout pakai form POST (bukan link GET) supaya tidak bisa dipicu sembarangan dari link luar --}}
            <form action="{{ route('logout') }}" method="POST">
              {{-- @csrf: token keamanan wajib di setiap form POST, mencegah serangan CSRF --}}
              @csrf
              <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<!--end::Header-->
