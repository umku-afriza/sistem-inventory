<?php
// File routes/api.php: daftar route API, yaitu URL yang mengembalikan data JSON (bukan halaman HTML).
// Biasanya dipakai aplikasi lain (mobile app, frontend JavaScript, Postman) untuk mengambil data.

// use: import class dari namespace lain agar bisa dipakai dengan nama pendek
use App\Http\Controllers\Api\ItemController; // controller API bahan (berbeda dengan ItemController untuk web)
use Illuminate\Support\Facades\Route; // Facade Route: untuk mendaftarkan route

/*
|--------------------------------------------------------------------------
| Route API
|--------------------------------------------------------------------------
| Semua route di file ini otomatis diawali "/api" dan mengembalikan JSON.
| throttle:60,1 = maksimal 60 request per menit per IP.
| name('api.') = nama route diawali "api." agar tidak bentrok dengan route web (items.index, dll).
*/
// middleware('throttle:60,1'): rate limiting (pembatasan request), maksimal 60 request per 1 menit.
// ->name('api.'): awalan (prefix) nama route. ->group(): semua route di dalamnya ikut aturan ini
Route::middleware('throttle:60,1')->name('api.')->group(function () {
    // Route::apiResource: seperti Route::resource tapi tanpa create & edit (API tidak butuh halaman form).
    // ->only(['index', 'show']): hanya GET /api/items (daftar) dan GET /api/items/{item} (detail)
    Route::apiResource('items', ItemController::class)->only(['index', 'show']);
});
