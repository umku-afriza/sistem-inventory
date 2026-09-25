<?php
// File migration: membuat tabel "stock_movements" (riwayat transaksi barang masuk & keluar).
// Migration: "resep" untuk membuat/mengubah tabel database. Dijalankan dengan php artisan migrate

// use: import class dari namespace lain
use Illuminate\Database\Migrations\Migration; // class induk semua migration
use Illuminate\Database\Schema\Blueprint; // Blueprint: "cetak biru" tabel untuk mendefinisikan kolom
use Illuminate\Support\Facades\Schema; // Facade Schema: untuk membuat/menghapus tabel

// return new class extends Migration: anonymous class (class tanpa nama) turunan Migration
return new class extends Migration
{
    /**
     * Buat tabel "stock_movements" (riwayat barang masuk & keluar).
     * up(): dijalankan saat php artisan migrate
     */
    public function up(): void
    {
        // Schema::create: membuat tabel baru 'stock_movements'
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id(); // primary key 'id' (BIGINT auto increment)
            // foreignId + constrained(): foreign key ke tabel items (kolom id)
            // cascadeOnDelete = jika bahan dihapus, riwayatnya ikut terhapus
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            // Admin yang mencatat: foreign key ke tabel users, nullable = boleh kosong
            // nullOnDelete = jika admin dihapus, user_id diisi NULL sehingga riwayat tetap ada
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 10); // "in" atau "out", lihat App\Enums\MovementType
            $table->unsignedInteger('quantity'); // jumlah barang, angka tidak negatif
            $table->date('moved_at'); // tanggal transaksi (tipe DATE, tanpa jam)
            $table->string('note')->nullable(); // catatan/keterangan, boleh kosong
            $table->timestamps(); // kolom created_at & updated_at
        });
    }

    /**
     * Hapus tabel saat rollback.
     * down(): kebalikan up(), dijalankan saat php artisan migrate:rollback
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel 'stock_movements' jika ada
        Schema::dropIfExists('stock_movements');
    }
};
