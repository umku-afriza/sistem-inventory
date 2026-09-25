<?php
// File migration bawaan Laravel: membuat tabel jobs, job_batches, dan failed_jobs untuk fitur Queue (antrian pekerjaan).
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
        // Schema::create: tabel 'jobs' berisi antrian pekerjaan yang menunggu diproses (php artisan queue:work)
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // primary key 'id' auto increment
            $table->string('queue')->index(); // nama antrian; index = pencarian cepat
            $table->longText('payload'); // longText: data pekerjaan (teks sangat panjang)
            $table->unsignedSmallInteger('attempts'); // unsignedSmallInteger: angka kecil tidak negatif, jumlah percobaan
            $table->unsignedInteger('reserved_at')->nullable(); // unsignedInteger: angka tidak negatif; waktu mulai diproses, boleh NULL
            $table->unsignedInteger('available_at'); // waktu pekerjaan boleh mulai diproses
            $table->unsignedInteger('created_at'); // waktu pekerjaan dibuat
        });

        // Schema::create: tabel 'job_batches' untuk sekumpulan (batch) pekerjaan yang dijalankan bersama
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // id batch sebagai primary key
            $table->string('name'); // nama batch
            $table->integer('total_jobs'); // integer: angka, jumlah total pekerjaan
            $table->integer('pending_jobs'); // jumlah pekerjaan yang belum selesai
            $table->integer('failed_jobs'); // jumlah pekerjaan yang gagal
            $table->longText('failed_job_ids'); // daftar id pekerjaan yang gagal
            $table->mediumText('options')->nullable(); // mediumText: pengaturan tambahan, boleh NULL
            $table->integer('cancelled_at')->nullable(); // waktu batch dibatalkan
            $table->integer('created_at'); // waktu batch dibuat
            $table->integer('finished_at')->nullable(); // waktu batch selesai
        });

        // Schema::create: tabel 'failed_jobs' mencatat pekerjaan yang gagal beserta errornya
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // primary key 'id'
            $table->string('uuid')->unique(); // uuid: kode unik panjang; unique = tidak boleh sama
            $table->string('connection'); // koneksi queue yang dipakai
            $table->string('queue'); // nama antrian
            $table->longText('payload'); // data pekerjaan
            $table->longText('exception'); // pesan error (exception) lengkap
            $table->timestamp('failed_at')->useCurrent(); // useCurrent: default diisi waktu saat ini

            // index gabungan (composite index) dari 3 kolom untuk mempercepat pencarian
            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     * down(): kebalikan up(), dijalankan saat rollback (menghapus tabel)
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel jika ada
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
