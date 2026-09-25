<?php
// File ini: class induk (base controller) untuk semua controller di aplikasi.
// Dalam pola MVC, Controller adalah "C" yang menerima request, memproses data (Model), lalu mengembalikan tampilan (View).

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

// abstract class: class yang tidak bisa dibuat objeknya langsung (tidak bisa "new Controller"),
// hanya untuk diwarisi (extends) oleh controller lain. Tempat menaruh kode yang dipakai bersama.
abstract class Controller
{
    // (kosong) belum ada method bersama; semua controller lain tetap mewarisi class ini
}
