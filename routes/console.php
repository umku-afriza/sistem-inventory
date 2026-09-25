<?php
// File routes/console.php: tempat mendaftarkan perintah Artisan sederhana yang dijalankan lewat terminal.
// Contoh: php artisan inspire

// use: import class dari namespace lain
use Illuminate\Foundation\Inspiring; // class bawaan Laravel berisi kumpulan kutipan (quote) motivasi
use Illuminate\Support\Facades\Artisan; // Facade Artisan: untuk membuat/menjalankan perintah artisan

// Artisan::command: membuat perintah baru bernama 'inspire'. Isi closure (function) dijalankan saat perintah dipanggil
Artisan::command('inspire', function () {
    // $this->comment(): menampilkan teks di terminal. Inspiring::quote() = ambil satu kutipan acak
    $this->comment(Inspiring::quote());
// ->purpose(): deskripsi perintah, tampil saat menjalankan php artisan list
})->purpose('Display an inspiring quote');
