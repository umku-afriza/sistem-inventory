{{--
  Partial sidebar: menu navigasi di sisi kiri, disisipkan ke layouts/app.blade.php lewat @include('layouts.partials.sidebar').
--}}
<!--begin::Sidebar-->
{{-- app-sidebar: komponen sidebar AdminLTE; data-bs-theme="dark" membuat sidebar berwarna gelap --}}
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  {{-- sidebar-brand: logo + nama aplikasi di atas sidebar --}}
  <div class="sidebar-brand">
    {{-- route('dashboard'): URL dari nama route "dashboard" --}}
    <a href="{{ route('dashboard') }}" class="brand-link">
      {{-- asset(): URL ke file gambar di folder public/adminlte/assets/img/ --}}
      <img src="{{ asset('adminlte/assets/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image opacity-75 shadow" />
      <span class="brand-text fw-light">{{ config('app.name') }}</span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2" aria-label="Main navigation">
      {{-- sidebar-menu: daftar menu; data-lte-toggle="treeview" = fitur AdminLTE untuk menu bertingkat --}}
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" id="navigation">
        {{-- request()->routeIs() dipakai untuk memberi class "active" pada menu yang sedang dibuka --}}
        {{-- Operator ternary (kondisi ? 'a' : 'b'): jika route sekarang "dashboard" tulis 'active', jika tidak tulis '' --}}
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        {{-- nav-header: judul pengelompokan menu (tidak bisa diklik) --}}
        <li class="nav-header">MASTER DATA</li>
        {{-- routeIs('categories.*'): tanda * = wildcard, cocok untuk semua route categories (index, create, edit, dst) --}}
        <li class="nav-item">
          <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-tags"></i>
            <p>Kategori</p>
          </a>
        </li>
        {{-- menu Bahan: pola sama seperti menu Kategori --}}
        <li class="nav-item">
          <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-box-seam"></i>
            <p>Bahan</p>
          </a>
        </li>

        <li class="nav-header">TRANSAKSI</li>
        {{-- menu-menu berikut: pola sama (route() untuk URL, routeIs() untuk class active) --}}
        <li class="nav-item">
          <a href="{{ route('stock-movements.index') }}" class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-arrow-left-right"></i>
            <p>Barang Masuk / Keluar</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
            <p>Laporan Stok</p>
          </a>
        </li>

        <li class="nav-header">PENGATURAN</li>
        <li class="nav-item">
          <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-people"></i>
            <p>Manajemen Admin</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-gear"></i>
            <p>Update Profile</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->
