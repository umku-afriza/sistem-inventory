{{--
  Partial footer: bagian bawah halaman, disisipkan ke layouts/app.blade.php lewat @include('layouts.partials.footer').
--}}
<!--begin::Footer-->
{{-- app-footer: class AdminLTE untuk footer --}}
<footer class="app-footer">
  {{-- float-end: rata kanan; d-none d-sm-inline: disembunyikan di layar HP, tampil di layar lebih besar --}}
  <div class="float-end d-none d-sm-inline">Sistem Inventori Bahan Bootcamp</div>
  {{-- date('Y'): fungsi PHP untuk tahun sekarang, jadi copyright selalu update otomatis --}}
  <strong>Copyright &copy; {{ date('Y') }} {{ config('app.name') }}.</strong>
</footer>
<!--end::Footer-->
