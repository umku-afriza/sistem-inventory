<?php
// File migration: membuat tabel "categories" (kategori bahan, contoh: Alat Tulis, Elektronik).
// Migration: "resep" untuk membuat/mengubah tabel database. Dijalankan dengan php artisan migrate

// use: import class dari namespace lain
use Illuminate\Database\Migrations\Migration; // class induk semua migration
use Illuminate\Database\Schema\Blueprint; // Blueprint: "cetak biru" tabel untuk mendefinisikan kolom
use Illuminate\Support\Facades\Schema; // Facade Schema: untuk membuat/menghapus tabel

// return new class extends Migration: anonymous class (class tanpa nama) turunan Migration
return new class extends Migration
{
    /**
     * Buat tabel "categories" (kategori bahan).
     * up(): dijalankan saat php artisan migrate
     */
    public function up(): void
    {
        // Schema::create: membuat tabel baru 'categories'. $table (Blueprint) dipakai untuk menambah kolom
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // primary key 'id' (BIGINT auto increment)
            $table->string('name')->unique(); // string = VARCHAR(255); unique = nama kategori tidak boleh sama
            $table->text('description')->nullable(); // text = teks panjang; nullable = boleh kosong (NULL)
            $table->timestamps(); // kolom created_at & updated_at, diisi otomatis oleh Eloquent
        });
    }

    /**
     * Hapus tabel saat rollback.
     * down(): kebalikan up(), dijalankan saat php artisan migrate:rollback
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel 'categories' jika ada
        Schema::dropIfExists('categories');
    }
};
