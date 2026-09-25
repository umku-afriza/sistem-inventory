<?php
// File bootstrap/app.php: titik awal (bootstrap) aplikasi Laravel.
// Di sini aplikasi dibuat dan diatur: file route yang dipakai, middleware, dan penanganan error (exception).

// use: import class dari namespace lain
use Illuminate\Foundation\Application; // class inti aplikasi Laravel
use Illuminate\Foundation\Configuration\Exceptions; // pengatur penanganan error/exception
use Illuminate\Foundation\Configuration\Middleware; // pengatur middleware
use Illuminate\Http\Request; // class yang mewakili request (permintaan) dari browser/client

// return Application::configure(): membuat & mengatur aplikasi, lalu dikembalikan (return) ke public/index.php.
// basePath: dirname(__DIR__) = named argument; folder utama project (satu tingkat di atas folder bootstrap)
return Application::configure(basePath: dirname(__DIR__))
    // ->withRouting(): mendaftarkan file-file route yang dipakai aplikasi
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // route halaman web (browser)
        api: __DIR__.'/../routes/api.php', // route API, otomatis diberi awalan URL /api
        commands: __DIR__.'/../routes/console.php', // perintah artisan tambahan
        health: '/up', // health check: URL /up untuk mengecek apakah aplikasi hidup
    )
    // ->withMiddleware(): tempat mendaftarkan/mengatur middleware (penyaring request). Saat ini belum ada tambahan
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    // ->withExceptions(): mengatur cara aplikasi menampilkan error (exception)
    ->withExceptions(function (Exceptions $exceptions): void {
        // shouldRenderJsonWhen: error ditampilkan dalam format JSON (bukan halaman HTML) jika kondisinya true
        $exceptions->shouldRenderJsonWhen(
            // fn (...) => ...: arrow function. True jika URL diawali "api/" ATAU client meminta JSON (header Accept: application/json)
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    // ->create(): menyelesaikan konfigurasi dan membuat objek aplikasi
    })->create();
