<?php
// File routes/web.php: daftar route (alamat URL) untuk halaman web yang dibuka lewat browser.
// Route = "peta jalan" yang menghubungkan URL ke method di Controller. Otomatis memakai middleware "web" (session, cookie, CSRF).

// use: import class dari namespace lain, supaya cukup ditulis nama pendeknya (contoh: AuthController::class)
use App\Http\Controllers\AdminController; // controller untuk manajemen admin
use App\Http\Controllers\AuthController; // controller untuk login, register, logout
use App\Http\Controllers\CategoryController; // controller CRUD kategori
use App\Http\Controllers\DashboardController; // controller halaman dashboard
use App\Http\Controllers\ItemController; // controller CRUD bahan (item)
use App\Http\Controllers\ProfileController; // controller edit profil admin yang sedang login
use App\Http\Controllers\ReportController; // controller laporan & export
use App\Http\Controllers\StockMovementController; // controller transaksi stok masuk/keluar
use Illuminate\Support\Facades\Route; // Facade Route: "pintu" untuk mendaftarkan route

// Route::redirect: halaman utama "/" langsung diarahkan (redirect) ke "/dashboard"
Route::redirect('/', '/dashboard');

/*
|--------------------------------------------------------------------------
| Route untuk TAMU (belum login)
|--------------------------------------------------------------------------
*/
// middleware('guest'): hanya tamu (belum login) yang boleh membuka route di dalam group ini; user yang sudah login diarahkan keluar.
// ->group(function () {...}): mengelompokkan banyak route agar memakai aturan (middleware) yang sama
Route::middleware('guest')->group(function () {
    // Route::get: jika browser membuka URL /login dengan method GET, jalankan method showLogin di AuthController.
    // [AuthController::class, 'showLogin'] = [nama class controller, nama method]
    // ->name('login'): memberi nama route, sehingga bisa dipanggil dengan route('login') di view/controller
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Route::post: menerima kiriman form login (method POST), diproses oleh method login
    Route::post('/login', [AuthController::class, 'login']);

    // Route::get: menampilkan halaman form register, nama route 'register'
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Route::post: menerima kiriman form register, disimpan oleh method register
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Route untuk ADMIN (sudah login)
|--------------------------------------------------------------------------
*/
// middleware('auth'): hanya user yang sudah login yang boleh membuka route di dalam group ini.
// Tamu yang belum login otomatis diarahkan ke route bernama 'login'
Route::middleware('auth')->group(function () {
    // Route::post logout: logout memakai POST (bukan GET) agar terlindungi token CSRF
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route::get: halaman dashboard, ditangani method index di DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master data
    // Route::resource: membuat 7 route CRUD sekaligus (index, create, store, show, edit, update, destroy).
    // ->except('show'): semua route dibuat KECUALI 'show' (halaman detail kategori tidak dipakai)
    Route::resource('categories', CategoryController::class)->except('show');
    // Route::resource: 7 route CRUD lengkap untuk bahan. Nama route-nya: items.index, items.create, items.store, dst
    Route::resource('items', ItemController::class);

    // Transaksi stok: hanya lihat & tambah (tidak ada edit/hapus)
    // ->only([...]): HANYA membuat route yang disebutkan: index (daftar), create (form), store (simpan)
    Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);

    // Laporan
    // Route::get: halaman laporan, nama route 'reports.index'
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    // Route::get: download/export laporan, nama route 'reports.export'
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Manajemen admin: index, create, store, edit, update, destroy
    // Route::resource + ->except('show'): 6 route CRUD admin tanpa halaman detail
    Route::resource('admin', AdminController::class)->except('show');

    // Update profile
    // Route::get: menampilkan form edit profil, nama route 'profile.edit'
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::put: method PUT dipakai untuk UPDATE data (di form HTML ditulis dengan @method('PUT'))
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
