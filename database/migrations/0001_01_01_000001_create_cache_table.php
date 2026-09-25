<?php
// File migration bawaan Laravel: membuat tabel cache & cache_locks (dipakai jika CACHE_STORE=database).
// Migration: "resep" untuk membuat/mengubah tabel database. Dijalankan dengan php artisan migrate

// use: import class dari namespace lain
use Illuminate\Database\Migrations\Migration; // class induk semua migration
use Illuminate\Database\Schema\Blueprint; // Blueprint: "cetak biru" tabel untuk mendefinisikan kolom
use Illuminate\Support\Facades\Schema; // Facade Schema: untuk membuat/menghapus tabel

// return new class extends Migration: anonymous class (class tanpa nama) turunan Migration
return new class extends Migration
{
    /**
     * Run the migrations.
     * up(): dijalankan saat php artisan migrate (membuat tabel)
     */
    public function up(): void
    {
        // Schema::create: tabel 'cache' untuk menyimpan data sementara agar aplikasi lebih cepat
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // nama/kunci cache sebagai primary key
            $table->mediumText('value'); // mediumText: teks agak panjang, isi data cache
            $table->bigInteger('expiration')->index(); // bigInteger: angka besar, waktu kedaluwarsa; index = pencarian cepat
        });

        // Schema::create: tabel 'cache_locks' untuk fitur lock (mencegah proses yang sama berjalan bersamaan)
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // nama lock sebagai primary key
            $table->string('owner'); // pemilik lock
            $table->bigInteger('expiration')->index(); // waktu lock kedaluwarsa
        });
    }

    /**
     * Reverse the migrations.
     * down(): kebalikan up(), dijalankan saat rollback (menghapus tabel)
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel jika ada
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
