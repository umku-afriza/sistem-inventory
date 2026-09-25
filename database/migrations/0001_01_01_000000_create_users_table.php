<?php
// File migration bawaan Laravel: membuat tabel users (akun admin), password_reset_tokens, dan sessions.
// Migration: "resep" untuk membuat/mengubah tabel database. Dijalankan dengan php artisan migrate

// use: import class dari namespace lain
use Illuminate\Database\Migrations\Migration; // class induk semua migration
use Illuminate\Database\Schema\Blueprint; // Blueprint: "cetak biru" tabel, dipakai untuk mendefinisikan kolom
use Illuminate\Support\Facades\Schema; // Facade Schema: untuk membuat/menghapus tabel

// return new class extends Migration: anonymous class (class tanpa nama) turunan Migration, langsung dikembalikan ke Laravel
return new class extends Migration
{
    /**
     * Run the migrations.
     * up(): dijalankan saat php artisan migrate (membuat tabel)
     */
    public function up(): void
    {
        // Schema::create: membuat tabel baru 'users'. $table (Blueprint) dipakai untuk menambah kolom
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // primary key 'id' (BIGINT auto increment)
            $table->string('name'); // VARCHAR(255) untuk nama
            $table->string('email')->unique(); // unique: email tidak boleh ada yang sama
            $table->timestamp('email_verified_at')->nullable(); // waktu verifikasi email; nullable = boleh kosong (NULL)
            $table->string('password'); // password yang sudah di-hash (dienkripsi satu arah)
            $table->string('photo')->nullable(); // lokasi file foto profil
            $table->rememberToken(); // kolom remember_token untuk fitur "Ingat Saya" saat login
            $table->timestamps(); // membuat kolom created_at & updated_at (diisi otomatis oleh Eloquent)
        });

        // Schema::create: tabel penyimpan token untuk fitur reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // primary: email dijadikan primary key
            $table->string('token'); // token rahasia reset password
            $table->timestamp('created_at')->nullable(); // waktu token dibuat
        });

        // Schema::create: tabel penyimpan session (data login) jika SESSION_DRIVER=database
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // id session sebagai primary key
            $table->foreignId('user_id')->nullable()->index(); // id user pemilik session; index = mempercepat pencarian
            $table->string('ip_address', 45)->nullable(); // alamat IP, panjang maks 45 karakter (cukup untuk IPv6)
            $table->text('user_agent')->nullable(); // text: teks panjang, info browser yang dipakai
            $table->longText('payload'); // longText: teks sangat panjang, isi data session
            $table->integer('last_activity')->index(); // waktu aktivitas terakhir (angka timestamp)
        });
    }

    /**
     * Reverse the migrations.
     * down(): kebalikan up(), dijalankan saat php artisan migrate:rollback (menghapus tabel)
     */
    public function down(): void
    {
        // Schema::dropIfExists: hapus tabel jika tabel tersebut ada
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
