<?php
// File AdminSeeder: mengisi 25 data admin dummy ke tabel users.
// Seeder: mengisi data awal ke database. Jalankan sendiri dengan php artisan db:seed --class=AdminSeeder

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/seeders)
namespace Database\Seeders;

// use: import class dari namespace lain
use App\Models\User; // model User (tabel users)
use Illuminate\Database\Seeder; // class induk semua seeder

/**
 * Data dummy admin untuk demo pencarian & pagination di menu Manajemen Admin.
 * Semua admin dummy memakai password: password
 */
// class AdminSeeder extends Seeder: turunan (inheritance) dari class Seeder
class AdminSeeder extends Seeder
{
    // run(): method yang otomatis dijalankan saat seeder dipanggil
    public function run(): void
    {
        // fake()->seed(2026): Seed tetap = data acak yang dihasilkan selalu sama setiap kali seeder dijalankan
        fake()->seed(2026);
        // fake()->unique(true): Reset daftar nilai unique() yang sudah terpakai, agar email yang dihasilkan juga selalu sama
        fake()->unique(true);

        // foreach + range(1, 25): perulangan (loop) sebanyak 25 kali, $number berisi 1, 2, ... 25
        foreach (range(1, 25) as $number) {
            // make() membuat objek tanpa menyimpan, lalu firstOrCreate mencegah data dobel jika seeder dijalankan ulang
            $admin = User::factory()->make();

            // firstOrCreate: cari user berdasarkan email; jika belum ada, buat baru dengan data di array kedua
            User::firstOrCreate(['email' => $admin->email], [
                'name' => $admin->name,
                'password' => $admin->password, // sudah dalam bentuk hash dari UserFactory
                'created_at' => fake()->dateTimeBetween('-1 year'), // tanggal dibuat acak dalam 1 tahun terakhir
            ]);
        }
    }
}
