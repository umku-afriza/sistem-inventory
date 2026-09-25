<?php
// File Service Provider AppServiceProvider: tempat pengaturan awal aplikasi.
// Laravel menjalankan provider ini setiap kali aplikasi dinyalakan (setiap request).

// namespace: "alamat" class ini, sesuai folder app/Providers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Providers;

use Illuminate\Pagination\Paginator; // import class Paginator: pengatur tampilan pagination (nomor halaman)
use Illuminate\Support\ServiceProvider; // import class ServiceProvider: induk semua service provider Laravel

// class AppServiceProvider extends ServiceProvider: mewarisi fitur service provider dari Laravel
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // method register(): tahap pertama, untuk mendaftarkan service/class ke service container. ": void" = tidak mengembalikan nilai
    public function register(): void
    {
        // (kosong) belum ada service yang perlu didaftarkan
    }

    /**
     * Bootstrap any application services.
     */
    // method boot(): tahap kedua, dijalankan setelah semua provider terdaftar. Cocok untuk pengaturan global
    public function boot(): void
    {
        // Tampilan pagination memakai Bootstrap 5 (sesuai AdminLTE)
        // Paginator::useBootstrapFive(): pemanggilan method static (::) agar {{ $items->links() }} memakai HTML Bootstrap 5
        Paginator::useBootstrapFive();
    }
}
