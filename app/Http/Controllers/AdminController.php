<?php
// File ini: AdminController, controller untuk mengelola data admin (CRUD).
// Peran di MVC: "C" (Controller) yang memakai Model User dan menampilkan View di folder resources/views/admin.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Models\User; // import Model User: mewakili tabel "users" (admin disimpan di sini)
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Facades\Auth; // import Facade Auth: info user yang sedang login
use Illuminate\Support\Facades\Hash; // import Facade Hash: untuk mengenkripsi (hash) password

/**
 * Manajemen Admin (CRUD: Create, Read, Update, Delete).
 * Data admin disimpan di tabel "users".
 */
// class + extends (pewarisan/inheritance): AdminController mewarisi semua kemampuan class Controller
class AdminController extends Controller
{
    /**
     * READ - Tampilkan daftar admin.
     */
    // method index(): menampilkan daftar data. Nama "index" adalah konvensi Route::resource
    public function index(Request $request)
    {
        // $request->search: ambil nilai query string ?search=... dari URL (null jika tidak ada)
        $search = $request->search;

        // User::query(): mulai membangun query Eloquent ke tabel users (Query Builder)
        $admins = User::query()
            // Jika ada kata pencarian, filter berdasarkan nama atau email
            // when($kondisi, closure): closure hanya dijalankan jika $search berisi nilai.
            // "function ($query) use ($search)": closure (fungsi tanpa nama); "use" membawa variabel $search dari luar ke dalam closure
            ->when($search, function ($query) use ($search) {
                // where(kolom, 'like', "%...%"): cari yang mengandung kata; "{$search}" = string interpolation (sisipkan variabel ke teks)
                $query->where('name', 'like', "%{$search}%")
                      // orWhere(): kondisi "ATAU", jadi cocok di nama ATAU email
                      ->orWhere('email', 'like', "%{$search}%");
            })
            // latest(): urutkan dari data terbaru (created_at paling baru di atas)
            ->latest()
            // paginate(10): ambil 10 data per halaman + info halaman (untuk link 1, 2, 3, ...)
            ->paginate(10)
            // withQueryString(): link pagination tetap membawa ?search=... agar filter tidak hilang saat pindah halaman
            ->withQueryString();

        // compact('admins', 'search'): buat array ['admins' => $admins, 'search' => $search] untuk dikirim ke view
        return view('admin.index', compact('admins', 'search'));
    }

    /**
     * CREATE - Tampilkan form tambah admin.
     */
    // method create(): konvensi Route::resource untuk menampilkan form tambah data
    public function create()
    {
        // view(): tampilkan resources/views/admin/create.blade.php
        return view('admin.create');
    }

    /**
     * CREATE - Simpan admin baru ke database.
     */
    // method store(): konvensi Route::resource untuk menyimpan data baru dari form (HTTP POST)
    public function store(Request $request)
    {
        // validate(): cek input; jika gagal otomatis kembali ke form dengan pesan error
        $request->validate([
            'name'     => 'required|string|max:255', // wajib, teks, maksimal 255 karakter
            'email'    => 'required|email|unique:users,email', // unique = email belum dipakai di tabel users
            'password' => 'required|min:8|confirmed', // minimal 8 karakter & harus sama dengan password_confirmation
        ]);

        // User::create([...]): Eloquent mass assignment, INSERT satu baris baru ke tabel users
        User::create([
            'name'     => $request->name, // ambil nilai input "name"
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hash::make(): simpan password dalam bentuk terenkripsi
        ]);

        // redirect()->route()->with(): pindah ke halaman daftar admin + pesan flash session (tampil sekali saja)
        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * UPDATE - Tampilkan form edit admin.
     */
    // Route Model Binding: "User $admin" -> Laravel otomatis mencari User berdasarkan {admin} di URL, jika tidak ada -> 404
    public function edit(User $admin)
    {
        // kirim data $admin ke view form edit
        return view('admin.edit', compact('admin'));
    }

    /**
     * UPDATE - Simpan perubahan data admin.
     */
    // method update(): konvensi Route::resource (HTTP PUT/PATCH); menerima $request dan $admin (Route Model Binding)
    public function update(Request $request, User $admin)
    {
        // validate(): cek input sebelum disimpan
        $request->validate([
            'name'     => 'required|string|max:255',
            // Email harus unik, kecuali milik admin ini sendiri
            // ". $admin->id": penggabungan string (concatenation), hasilnya misalnya "unique:users,email,5"
            'email'    => 'required|email|unique:users,email,' . $admin->id,
            // Password boleh kosong (tidak diganti)
            // nullable = boleh kosong; jika diisi, tetap harus min 8 & confirmed
            'password' => 'nullable|min:8|confirmed',
        ]);

        // isi properti model satu per satu (belum tersimpan ke database sampai save())
        $admin->name  = $request->name;
        $admin->email = $request->email;

        // Ganti password hanya jika diisi
        // $request->filled('password'): true jika input password ada dan tidak kosong
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        // save(): simpan perubahan ke database (UPDATE)
        $admin->save();

        // redirect + pesan flash, sama seperti di store()
        return redirect()->route('admin.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * DELETE - Hapus admin.
     */
    // method destroy(): konvensi Route::resource untuk menghapus data (HTTP DELETE); $admin dari Route Model Binding
    public function destroy(User $admin)
    {
        // Admin tidak boleh menghapus akunnya sendiri
        // Auth::id(): ID user yang sedang login; "===" = perbandingan ketat (nilai dan tipe data harus sama)
        if ($admin->id === Auth::id()) {
            // back()->with(): kembali ke halaman sebelumnya + pesan flash "error"
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // delete(): hapus baris admin ini dari database
        $admin->delete();

        // redirect + pesan flash, sama seperti di store()
        return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus.');
    }
}
