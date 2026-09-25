<?php
// File ini: CategoryController, controller untuk mengelola kategori bahan (CRUD).
// Peran di MVC: "C" (Controller) yang memakai Model Category dan menampilkan View di folder resources/views/categories.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest; // import Form Request: class khusus berisi aturan validasi kategori
use App\Models\Category; // import Model Category: mewakili tabel "categories"
use Illuminate\Http\RedirectResponse; // import class RedirectResponse: return type untuk method yang melakukan redirect
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\View\View; // import class View: return type untuk method yang mengembalikan halaman

/**
 * Manajemen Kategori Bahan (CRUD).
 * Materi: Route::resource, Form Request, relasi hasMany & withCount.
 */
// class + extends (pewarisan/inheritance): CategoryController mewarisi semua kemampuan class Controller
class CategoryController extends Controller
{
    /**
     * READ - Tampilkan daftar kategori.
     */
    // method index(): menampilkan daftar data (konvensi Route::resource). Return type ": View" = wajib mengembalikan halaman
    public function index(Request $request): View
    {
        // $request->search: ambil nilai query string ?search=... dari URL
        $search = $request->search;

        // Category::query(): mulai membangun query Eloquent ke tabel categories
        $categories = Category::query()
            // withCount menambahkan kolom "items_count" (jumlah bahan per kategori) tanpa query tambahan per baris
            ->withCount('items')
            // when() + arrow function "fn ($query) => ...": fungsi singkat satu baris, otomatis bisa memakai $search dari luar (tanpa "use")
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            // orderBy('name'): urutkan berdasarkan nama A-Z
            ->orderBy('name')
            // paginate(10): 10 data per halaman
            ->paginate(10)
            // withQueryString(): link pagination tetap membawa ?search=...
            ->withQueryString();

        // compact(): buat array ['categories' => ..., 'search' => ...] untuk dikirim ke view
        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * CREATE - Tampilkan form tambah kategori.
     */
    // method create(): menampilkan form tambah data (konvensi Route::resource)
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * CREATE - Simpan kategori baru.
     * Validasi sudah dijalankan otomatis oleh CategoryRequest sebelum method ini dipanggil.
     */
    // method store(): simpan data baru. Type hint "CategoryRequest" = Form Request; return type ": RedirectResponse" = wajib redirect
    public function store(CategoryRequest $request): RedirectResponse
    {
        // validated() hanya berisi field yang lolos validasi
        // Category::create(): mass assignment, INSERT baris baru ke tabel categories
        Category::create($request->validated());

        // redirect()->route()->with(): pindah ke halaman daftar + pesan flash session (tampil sekali saja)
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * UPDATE - Tampilkan form edit kategori.
     */
    // Route Model Binding: Laravel otomatis mencari Category berdasarkan {category} di URL, jika tidak ada -> 404
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * UPDATE - Simpan perubahan kategori.
     */
    // method update(): validasi oleh CategoryRequest, $category didapat dari Route Model Binding
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        // update(): ubah kolom sesuai data tervalidasi lalu langsung simpan ke database
        $category->update($request->validated());

        // redirect + pesan flash, sama seperti di store()
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * DELETE - Hapus kategori.
     */
    // method destroy(): hapus data (konvensi Route::resource, HTTP DELETE)
    public function destroy(Category $category): RedirectResponse
    {
        // Kategori yang masih dipakai bahan tidak boleh dihapus
        // items(): relasi hasMany ke Item; exists(): true jika minimal ada 1 bahan di kategori ini
        if ($category->items()->exists()) {
            // back()->with(): kembali ke halaman sebelumnya + pesan flash "error"
            return back()->with('error', 'Kategori masih dipakai oleh bahan, tidak bisa dihapus.');
        }

        // delete(): hapus baris kategori ini dari database
        $category->delete();

        // redirect + pesan flash, sama seperti di store()
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
