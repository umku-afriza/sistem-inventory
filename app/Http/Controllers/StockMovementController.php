<?php
// File ini: StockMovementController, controller untuk transaksi stok (barang masuk & barang keluar).
// Peran di MVC: "C" (Controller) yang memakai Model StockMovement & Item dan menampilkan View resources/views/stock-movements.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Enums\MovementType; // import Enum MovementType: daftar nilai tetap jenis transaksi (In = masuk, Out = keluar)
use App\Http\Requests\StockMovementRequest; // import Form Request: aturan validasi transaksi stok
use App\Models\Item; // import Model Item: tabel "items" (bahan)
use App\Models\StockMovement; // import Model StockMovement: tabel "stock_movements" (riwayat transaksi)
use Illuminate\Http\RedirectResponse; // import class RedirectResponse: return type untuk method yang melakukan redirect
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Facades\Auth; // import Facade Auth: info user yang sedang login
use Illuminate\Support\Facades\DB; // import Facade DB: akses database langsung, di sini untuk transaction
use Illuminate\Validation\ValidationException; // import class ValidationException: error validasi yang bisa kita lempar sendiri
use Illuminate\View\View; // import class View: return type untuk method yang mengembalikan halaman

/**
 * Transaksi Stok: mencatat barang masuk dan barang keluar.
 * Materi: Enum, database transaction, lockForUpdate, increment/decrement.
 *
 * Transaksi sengaja tidak bisa diedit/dihapus, seperti buku kas:
 * jika salah input, buat transaksi koreksi.
 */
// class + extends (pewarisan/inheritance): StockMovementController mewarisi semua kemampuan class Controller
class StockMovementController extends Controller
{
    /**
     * READ - Tampilkan riwayat transaksi dengan filter.
     */
    // method index(): menampilkan daftar data (konvensi Route::resource). Return type ": View" = wajib mengembalikan halaman
    public function index(Request $request): View
    {
        // StockMovement::query(): mulai membangun query Eloquent ke tabel stock_movements
        $movements = StockMovement::query()
            // with([...]): eager loading relasi bahan & admin sekaligus, mencegah N+1 query
            ->with(['item', 'user'])
            // when() + arrow function "fn (...) => ...": filter hanya dipakai jika nilainya diisi; nilai dikirim sebagai parameter kedua
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            // filter berdasarkan bahan tertentu (?item_id=...)
            ->when($request->item_id, fn ($query, $itemId) => $query->where('item_id', $itemId))
            // whereDate(kolom, '>=', tanggal): bandingkan bagian tanggal saja; transaksi mulai tanggal awal
            ->when($request->start_date, fn ($query, $date) => $query->whereDate('moved_at', '>=', $date))
            // transaksi sampai tanggal akhir
            ->when($request->end_date, fn ($query, $date) => $query->whereDate('moved_at', '<=', $date))
            // latest('moved_at'): urut tanggal transaksi terbaru dulu
            ->latest('moved_at')
            // latest('id'): jika tanggal sama, urut dari id terbesar
            ->latest('id')
            // paginate(15): 15 data per halaman
            ->paginate(15)
            // withQueryString(): link pagination tetap membawa filter
            ->withQueryString();

        // daftar bahan untuk dropdown filter
        $items = Item::orderBy('name')->get();
        // MovementType::cases(): method bawaan Enum, mengembalikan semua pilihan (In, Out)
        $types = MovementType::cases();

        // compact(): kirim variabel ke view
        return view('stock-movements.index', compact('movements', 'items', 'types'));
    }

    /**
     * CREATE - Tampilkan form transaksi.
     * Bisa diisi otomatis lewat URL, contoh: /stock-movements/create?type=out&item_id=3
     */
    // method create(): menampilkan form tambah transaksi
    public function create(Request $request): View
    {
        // daftar bahan & jenis transaksi untuk pilihan di form
        $items = Item::orderBy('name')->get();
        $types = MovementType::cases();
        // tryFrom(): ubah teks jadi Enum, hasilnya null jika tidak valid (tidak error). (string) = type casting ke teks.
        // "??" (null coalescing): jika hasil kiri null, pakai nilai kanan (default: MovementType::In)
        $selectedType = MovementType::tryFrom((string) $request->type) ?? MovementType::In;
        // bahan yang dipilih otomatis dari URL (?item_id=...), boleh null
        $selectedItemId = $request->item_id;

        return view('stock-movements.create', compact('items', 'types', 'selectedType', 'selectedItemId'));
    }

    /**
     * CREATE - Simpan transaksi dan perbarui stok bahan.
     */
    // method store(): simpan data baru. "StockMovementRequest" = Form Request (validasi otomatis); ": RedirectResponse" = wajib redirect
    public function store(StockMovementRequest $request): RedirectResponse
    {
        // validated(): array field yang lolos validasi
        $data = $request->validated();
        // MovementType::from(): ubah teks ("in"/"out") menjadi Enum; error jika nilainya tidak valid
        $type = MovementType::from($data['type']);

        // DB::transaction(closure): semua query di dalam dijalankan sebagai satu kesatuan; jika ada error, semua dibatalkan (rollback).
        // "use ($data, $type)": membawa variabel dari luar ke dalam closure
        DB::transaction(function () use ($data, $type) {
            // lockForUpdate mengunci baris bahan ini sampai transaksi selesai,
            // sehingga dua admin yang menyimpan bersamaan tidak membuat stok menjadi salah
            // findOrFail(): cari berdasarkan id, jika tidak ketemu -> error 404
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            // cek: jika barang keluar ("&&" = DAN) jumlahnya melebihi stok yang ada
            if ($type === MovementType::Out && $data['quantity'] > $item->stock) {
                // Melempar error validasi: otomatis kembali ke form dengan pesan error
                // throw: melempar exception (menghentikan proses); withMessages([...]): pesan error per field
                throw ValidationException::withMessages([
                    // "{$item->name}": string interpolation, menyisipkan nilai properti ke dalam teks
                    'quantity' => "Stok {$item->name} tidak cukup. Stok tersedia: {$item->stock} {$item->unit}.",
                ]);
            }

            // [...$data, 'user_id' => ...]: spread operator, salin semua isi $data lalu tambahkan user_id.
            // stockMovements()->create(): simpan transaksi lewat relasi (item_id terisi otomatis)
            $item->stockMovements()->create([...$data, 'user_id' => Auth::id()]);

            // jika barang masuk, stok ditambah; selain itu (keluar), stok dikurangi
            if ($type === MovementType::In) {
                // increment(kolom, jumlah): UPDATE stock = stock + jumlah langsung di database
                $item->increment('stock', $data['quantity']);
            } else {
                // decrement(kolom, jumlah): UPDATE stock = stock - jumlah
                $item->decrement('stock', $data['quantity']);
            }
        });

        // redirect()->route(): pindah ke halaman riwayat transaksi
        return redirect()->route('stock-movements.index')
            // with(): pesan flash session; {$type->label()} = memanggil method label() milik Enum di dalam string
            ->with('success', "Transaksi barang {$type->label()} berhasil dicatat.");
    }
}
