<?php
// File migration: membuat tabel "items" (bahan/barang inventori). Setiap bahan milik satu kategori.
// Migration: "resep" untuk membuat/mengubah tabel database. Dijalankan dengan php artisan migrate

// use: import class dari namespace lain
use Illuminate\Database\Migrations\Migration; // class induk semua migration
use Illuminate\Database\Schema\Blueprint; // Blueprint: "cetak biru" tabel untuk mendefinisikan kolom
use Illuminate\Support\Facades\Schema; // Facade Schema: untuk membuat/menghapus tabel

// return new class extends Migration: anonymous class (class tanpa nama) turunan Migration
return new class extends Migration
{
    /**
     * Buat tabel "items" (bahan-bahan bootcamp).
     * up(): dijalankan saat php artisan migrate
     */
    public function up(): void
    {
        // Schema::create: membuat tabel baru 'items'
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // primary key 'id' (BIGINT auto increment)
            // Relasi ke tabel categories. restrictOnDelete = kategori tidak bisa dihapus jika masih dipakai
            // foreignId: kolom BIGINT untuk foreign key; constrained(): otomatis terhubung ke tabel categories kolom id
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique(); // kode bahan, contoh: BHN-001; unique = tidak boleh sama
            $table->string('name'); // nama bahan, VARCHAR(255)
            $table->string('unit', 20); // satuan: pcs, rim, box, dll (panjang maks 20 karakter)
            $table->unsignedInteger('stock')->default(0); // unsignedInteger = angka tidak negatif; default(0) = nilai awal 0
            $table->unsignedInteger('min_stock')->default(0); // batas stok dianggap menipis
            $table->string('photo')->nullable(); // lokasi file foto; nullable = boleh kosong (NULL)
            $table->text('description')->nullable(); // text = teks panjang, deskripsi bahan
            $table->timestamps(); // kolom created_at & updated_at
        });
    }

    /**
     * Hapus tabel saat rollback.
     * down(): kebalikan up(), dijalankan saat php artisan migrate:rollback
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel 'items' jika ada
        Schema::dropIfExists('items');
    }
};
