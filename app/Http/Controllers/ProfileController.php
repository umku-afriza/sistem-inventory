<?php
// File ini: ProfileController, controller untuk mengubah profil admin yang sedang login (nama, email, password, foto).
// Peran di MVC: "C" (Controller) yang memakai Model User (lewat Auth) dan menampilkan View resources/views/profile.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Facades\Auth; // import Facade Auth: mengambil user yang sedang login
use Illuminate\Support\Facades\Hash; // import Facade Hash: untuk mengenkripsi (hash) password
use Illuminate\Support\Facades\Storage; // import Facade Storage: mengelola file (simpan/hapus) di disk penyimpanan

// class + extends (pewarisan/inheritance): ProfileController mewarisi semua kemampuan class Controller
class ProfileController extends Controller
{
    /**
     * Tampilkan form update profile milik admin yang sedang login.
     */
    // method edit(): menampilkan form edit profil (tanpa parameter, karena yang diedit selalu user yang login)
    public function edit()
    {
        // Auth::user(): ambil objek User yang sedang login
        $user = Auth::user();

        // compact('user'): kirim ['user' => $user] ke view resources/views/profile/edit.blade.php
        return view('profile.edit', compact('user'));
    }

    /**
     * Simpan perubahan profile.
     */
    // method update(): menyimpan perubahan profil; "Request $request" = dependency injection data dari browser
    public function update(Request $request)
    {
        // ambil user yang sedang login (yang profilnya akan diubah)
        $user = Auth::user();

        // validate(): cek input; jika gagal otomatis kembali ke form dengan pesan error
        $request->validate([
            'name'     => 'required|string|max:255', // wajib, teks, maksimal 255 karakter
            'email'    => 'required|email|unique:users,email,' . $user->id, // unik, kecuali email milik user ini sendiri
            'password' => 'nullable|min:8|confirmed', // boleh kosong; jika diisi min 8 & harus sama dengan konfirmasi
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // maks 2 MB
        ]);

        // isi properti model (belum tersimpan sampai save())
        $user->name  = $request->name;
        $user->email = $request->email;

        // Ganti password hanya jika diisi
        // $request->filled(): true jika input ada dan tidak kosong
        if ($request->filled('password')) {
            // Hash::make(): enkripsi password sebelum disimpan
            $user->password = Hash::make($request->password);
        }

        // Upload foto baru jika ada
        // $request->hasFile('photo'): true jika user memilih file pada input "photo"
        if ($request->hasFile('photo')) {
            // Hapus foto lama agar tidak menumpuk
            if ($user->photo) {
                // Storage::disk('public')->delete(): hapus file dari disk "public" (storage/app/public)
                Storage::disk('public')->delete($user->photo);
            }

            // Simpan ke folder storage/app/public/photos
            // file('photo')->store('photos', 'public'): upload file dengan nama acak, hasilnya path file (misal "photos/abc.jpg")
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        // save(): simpan semua perubahan ke database (UPDATE)
        $user->save();

        // redirect()->route()->with(): kembali ke form profil + pesan flash session (tampil sekali saja)
        return redirect()->route('profile.edit')->with('success', 'Profile berhasil diperbarui.');
    }
}
