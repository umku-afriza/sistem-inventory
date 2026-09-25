<?php
// File ini: DashboardController, controller untuk halaman dashboard (ringkasan angka & data terbaru).
// Peran di MVC: "C" (Controller) yang mengambil data dari beberapa Model lalu mengirimnya ke View "dashboard".

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Models\Category; // import Model Category: tabel "categories"
use App\Models\Item; // import Model Item: tabel "items" (bahan)
use App\Models\StockMovement; // import Model StockMovement: tabel "stock_movements" (transaksi stok)
use App\Models\User; // import Model User: tabel "users" (admin)
use Illuminate\View\View; // import class View: dipakai sebagai return type method yang mengembalikan halaman

// class + extends (pewarisan/inheritance): DashboardController mewarisi semua kemampuan class Controller
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     */
    // method index() dengan return type ": View": method ini wajib mengembalikan halaman (view)
    public function index(): View
    {
        // Model::count(): hitung jumlah baris di tabel (SELECT COUNT(*))
        $totalAdmin = User::count();
        $totalCategory = Category::count();
        $totalItem = Item::count();
        // lowStock(): query scope (filter siap pakai) dari model Item, lalu dihitung dengan count()
        $totalLowStock = Item::lowStock()->count();

        // 5 bahan dengan stok menipis, diurutkan dari yang paling sedikit
        // orderBy('stock'): urut stok kecil ke besar; limit(5): ambil 5 saja; get(): jalankan query, hasilnya Collection
        $lowStockItems = Item::lowStock()->orderBy('stock')->limit(5)->get();

        // 5 transaksi terakhir
        // with(['item', 'user']): eager loading relasi agar tidak N+1 query; latest('id'): urut dari id terbesar (terbaru)
        $latestMovements = StockMovement::with(['item', 'user'])->latest('id')->limit(5)->get();

        // view() + compact(): kirim semua variabel di bawah ke resources/views/dashboard.blade.php
        return view('dashboard', compact(
            'totalAdmin',
            'totalCategory',
            'totalItem',
            'totalLowStock',
            'lowStockItems',
            'latestMovements',
        ));
    }
}
