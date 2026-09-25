<?php
// File DatabaseSeeder: seeder utama yang dijalankan pertama kali, lalu memanggil seeder-seeder lainnya.
// Seeder: mengisi data awal ke database. Dijalankan dengan php artisan db:seed

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/seeders)
namespace Database\Seeders;

// use: import class dari namespace lain
use App\Models\User; // model User (tabel users)
use Illuminate\Database\Seeder; // class induk semua seeder
use Illuminate\Support\Facades\Hash; // Facade Hash: untuk mengenkripsi (hash) password

// class DatabaseSeeder extends Seeder: turunan (inheritance) dari class Seeder
class DatabaseSeeder extends Seeder
{
    /**
     * Isi data awal ke database.
     * Jalankan dengan: php artisan db:seed
     * run(): method yang otomatis dijalankan saat seeder dipanggil
     */
    public function run(): void
    {
        // Admin pertama, supaya bisa langsung login.
        // firstOrCreate = hanya dibuat jika email tersebut belum ada
        // Parameter 1: kolom untuk mencari; parameter 2: kolom tambahan yang diisi saat data baru dibuat
        User::firstOrCreate(
            ['email' => 'admin@bootcamp.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')], // Hash::make: password disimpan dalam bentuk hash
        );

        // Data dummy untuk demo pencarian, filter, dan pagination
        // $this->call([...]): menjalankan seeder lain secara berurutan
        $this->call([
            AdminSeeder::class,     // 25 admin dummy
            InventorySeeder::class, // 13 kategori, 71 bahan, riwayat stok 6 bulan
        ]);
    }
}
