<?php
// File ini: ItemController, controller untuk mengelola data bahan (CRUD + halaman detail + upload foto).
// Peran di MVC: "C" (Controller) yang memakai Model Item & Category dan menampilkan View di folder resources/views/items.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Enums\MovementType; // import Enum MovementType: daftar nilai tetap jenis transaksi (In = masuk, Out = keluar)
use App\Http\Requests\ItemRequest; // import Form Request: class berisi aturan validasi bahan
use App\Models\Category; // import Model Category: tabel "categories"
use App\Models\Item; // import Model Item: tabel "items" (bahan)
use Illuminate\Http\RedirectResponse; // import class RedirectResponse: return type untuk method yang melakukan redirect
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Facades\Auth; // import Facade Auth: info user yang sedang login
use Illuminate\Support\Facades\DB; // import Facade DB: akses database langsung, di sini untuk transaction
use Illuminate\Support\Facades\Storage; // import Facade Storage: mengelola file (simpan/hapus) di disk penyimpanan
use Illuminate\View\View; // import class View: return type untuk method yang mengembalikan halaman

/**
 * Manajemen Bahan (CRUD + detail).
 * Materi: relasi belongsTo, eager loading, filter, upload file, query scope, database transaction.
 */
// class + extends (pewarisan/inheritance): ItemController mewarisi semua kemampuan class Controller
class ItemController extends Controller
{
    /**
     * READ - Tampilkan daftar bahan dengan filter.
     */
    // method index(): menampilkan daftar data (konvensi Route::resource). Return type ": View" = wajib mengembalikan halaman
    public function index(Request $request): View
    {
        // Item::query(): mulai membangun query Eloquent ke tabel items
        $items = Item::query()
            // Eager loading: ambil data kategori sekaligus agar tidak terjadi masalah N+1 query
            ->with('category')
            // when($request->search, closure): closure hanya jalan jika ?search= diisi; nilainya dikirim sebagai parameter kedua ($search)
            ->when($request->search, function ($query, $search) {
                // where(function ...): pengelompokan kondisi (grouping), jadi SQL-nya: WHERE (name LIKE ... OR code LIKE ...).
                // "use ($search)": membawa variabel $search ke dalam closure bagian dalam
                $query->where(function ($query) use ($search) {
                    // "%{$search}%": string interpolation, mencari teks yang mengandung kata kunci
                    $query->where('name', 'like', "%{$search}%")
                        // orWhere(): kondisi "ATAU", cocok di nama ATAU kode
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            // arrow function "fn (...) => ...": fungsi singkat satu baris; filter kategori jika ?category_id= diisi
            ->when($request->category_id, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            // Memakai query scope lowStock() yang didefinisikan di model Item
            // $request->boolean('low_stock'): ubah input (1/"on"/true) menjadi true/false
            ->when($request->boolean('low_stock'), fn ($query) => $query->lowStock())
            // orderBy('name'): urutkan nama A-Z
            ->orderBy('name')
            // paginate(10): 10 data per halaman
            ->paginate(10)
            // withQueryString(): link pagination tetap membawa filter (?search=..&category_id=..)
            ->withQueryString();

        // ambil semua kategori urut nama, untuk pilihan dropdown filter; get() = jalankan query, hasilnya Collection
        $categories = Category::orderBy('name')->get();

        // compact(): kirim ['items' => ..., 'categories' => ...] ke view
        return view('items.index', compact('items', 'categories'));
    }

    /**
     * CREATE - Tampilkan form tambah bahan.
     */
    // method create(): menampilkan form tambah data (konvensi Route::resource)
    public function create(): View
    {
        // daftar kategori untuk pilihan dropdown di form
        $categories = Category::orderBy('name')->get();

        return view('items.create', compact('categories'));
    }

    /**
     * CREATE - Simpan bahan baru beserta riwayat stok awalnya.
     */
    // method store(): simpan data baru. "ItemRequest" = Form Request (validasi otomatis); ": RedirectResponse" = wajib redirect
    public function store(ItemRequest $request): RedirectResponse
    {
        // validated(): array berisi field yang lolos validasi saja
        $data = $request->validated();

        // hasFile('photo'): true jika user mengunggah file foto
        if ($request->hasFile('photo')) {
            // store('items', 'public'): simpan file ke storage/app/public/items dengan nama acak; hasilnya path file disimpan ke $data
            $data['photo'] = $request->file('photo')->store('items', 'public');
        }

        // Transaction: bahan dan riwayat stok awal harus tersimpan bersama.
        // Jika salah satu gagal, semuanya dibatalkan (rollback).
        // DB::transaction(closure): semua query di dalam closure dijalankan sebagai satu kesatuan; "use ($data)" membawa $data ke dalam
        DB::transaction(function () use ($data) {
            // Item::create(): INSERT bahan baru, hasilnya objek Item (sudah punya id)
            $item = Item::create($data);

            // hanya catat riwayat jika stok awal lebih dari 0
            if ($item->stock > 0) {
                // stockMovements()->create(): relasi hasMany, membuat transaksi stok yang otomatis terisi item_id bahan ini
                $item->stockMovements()->create([
                    'user_id' => Auth::id(), // Auth::id(): ID admin yang sedang login
                    'type' => MovementType::In, // nilai Enum: jenis transaksi "masuk"
                    'quantity' => $item->stock, // jumlah = stok awal
                    'moved_at' => today(), // today(): helper tanggal hari ini (jam 00:00)
                    'note' => 'Stok awal', // catatan transaksi
                ]);
            }
        });

        // redirect()->route()->with(): pindah ke halaman daftar + pesan flash session (tampil sekali saja)
        return redirect()->route('items.index')->with('success', 'Bahan berhasil ditambahkan.');
    }

    /**
     * READ - Tampilkan detail bahan dan riwayat stoknya.
     */
    // method show(): tampilkan detail satu data. Route Model Binding: Item dicari otomatis dari {item} di URL, jika tidak ada -> 404
    public function show(Item $item): View
    {
        // load('category'): lazy eager loading, memuat relasi kategori untuk model yang sudah ada
        $item->load('category');

        // stockMovements(): query riwayat transaksi milik bahan ini saja
        $movements = $item->stockMovements()
            // with('user'): eager loading admin pencatat transaksi
            ->with('user')
            // latest('moved_at'): urut tanggal transaksi terbaru dulu
            ->latest('moved_at')
            // latest('id'): jika tanggal sama, urut dari id terbesar
            ->latest('id')
            // paginate(10): 10 transaksi per halaman
            ->paginate(10);

        return view('items.show', compact('item', 'movements'));
    }

    /**
     * UPDATE - Tampilkan form edit bahan.
     */
    // method edit(): form edit; $item dari Route Model Binding
    public function edit(Item $item): View
    {
        // daftar kategori untuk dropdown
        $categories = Category::orderBy('name')->get();

        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * UPDATE - Simpan perubahan bahan (stok tidak ikut diubah di sini).
     */
    // method update(): validasi oleh ItemRequest, $item dari Route Model Binding
    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        // data yang lolos validasi
        $data = $request->validated();

        // upload foto baru jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama agar tidak menumpuk
            if ($item->photo) {
                // Storage::disk('public')->delete(): hapus file dari disk "public" (storage/app/public)
                Storage::disk('public')->delete($item->photo);
            }

            // simpan foto baru, sama seperti di store()
            $data['photo'] = $request->file('photo')->store('items', 'public');
        }

        // update(): ubah kolom sesuai $data lalu simpan ke database
        $item->update($data);

        // route('items.show', $item): route dengan parameter, menghasilkan URL /items/{id}; + pesan flash
        return redirect()->route('items.show', $item)->with('success', 'Bahan berhasil diperbarui.');
    }

    /**
     * DELETE - Hapus bahan beserta fotonya.
     */
    // method destroy(): hapus data (konvensi Route::resource, HTTP DELETE)
    public function destroy(Item $item): RedirectResponse
    {
        // hapus file foto dari storage jika bahan punya foto
        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }

        // Riwayat stok ikut terhapus karena cascadeOnDelete di migration
        // delete(): hapus baris bahan dari database
        $item->delete();

        // redirect + pesan flash, sama seperti di store()
        return redirect()->route('items.index')->with('success', 'Bahan berhasil dihapus.');
    }
}
