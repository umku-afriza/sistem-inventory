<?php
// File ini: AuthController, controller untuk autentikasi (login, register, logout).
// Peran di MVC: "C" (Controller) yang memakai Model User dan menampilkan View di folder resources/views/auth.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Models\User; // import Model User: mewakili tabel "users" di database
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Facades\Auth; // import Facade Auth: fitur login/logout & info user yang sedang login
use Illuminate\Support\Facades\Hash; // import Facade Hash: untuk mengenkripsi (hash) password

// class + extends (pewarisan/inheritance): AuthController mewarisi semua kemampuan class Controller
class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    // method (fungsi di dalam class) showLogin(): "public" artinya bisa dipanggil dari luar class (oleh Route)
    public function showLogin()
    {
        // return view(): kembalikan halaman Blade resources/views/auth/login.blade.php (titik "." = pemisah folder)
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    // parameter "Request $request": type hint + dependency injection, Laravel otomatis mengisi $request dengan data dari browser
    public function login(Request $request)
    {
        // 1. Validasi input
        // $request->validate([...]): cek aturan tiap field; jika gagal, otomatis kembali ke form + pesan error.
        // Hasilnya (array field yang lolos) disimpan ke variabel $credentials
        $credentials = $request->validate([
            'email'    => 'required|email', // required = wajib diisi, email = format harus email
            'password' => 'required', // wajib diisi
        ]);

        // 2. Cek email & password ke database
        // Auth::attempt(): cocokkan email + password (yang di-hash) dengan data di tabel users; true jika cocok lalu login.
        // $request->boolean('remember'): ubah input checkbox "remember" jadi true/false (fitur "ingat saya")
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Buat ulang session untuk keamanan
            // session()->regenerate(): ganti ID session baru, mencegah serangan "session fixation"
            $request->session()->regenerate();

            // redirect()->route(): arahkan browser ke URL milik route bernama "dashboard"
            return redirect()->route('dashboard');
        }

        // 3. Jika gagal, kembali ke form login dengan pesan error
        // back(): kembali ke halaman sebelumnya (form login)
        return back()
            // withErrors(): kirim pesan error ke view (tampil lewat $errors / @error di Blade)
            ->withErrors(['email' => 'Email atau password salah.'])
            // onlyInput('email'): isi ulang input email saja (password tidak dikembalikan demi keamanan)
            ->onlyInput('email');
    }

    /**
     * Tampilkan form register.
     */
    // method showRegister(): menampilkan halaman form daftar akun
    public function showRegister()
    {
        // return view(): tampilkan resources/views/auth/register.blade.php
        return view('auth.register');
    }

    /**
     * Proses register (daftar akun baru).
     */
    // method register(): menerima data form register lewat $request (dependency injection)
    public function register(Request $request)
    {
        // 1. Validasi input
        // validate(): jika ada aturan yang gagal, proses berhenti dan kembali ke form dengan pesan error
        $request->validate([
            'name'     => 'required|string|max:255', // wajib, berupa teks, maksimal 255 karakter
            'email'    => 'required|email|unique:users,email', // unique:users,email = email belum pernah dipakai di tabel users
            'password' => 'required|min:8|confirmed', // confirmed = harus sama dengan password_confirmation
        ]);

        // 2. Simpan user baru ke database
        // User::create([...]): Eloquent mass assignment, INSERT satu baris baru ke tabel users (kolom harus ada di $fillable)
        $user = User::create([
            'name'     => $request->name, // $request->name: ambil nilai input bernama "name"
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hash::make(): enkripsi password, jangan simpan password asli
        ]);

        // 3. Langsung login-kan user tersebut
        // Auth::login($user): login-kan user tanpa perlu cek password lagi
        Auth::login($user);

        // redirect + with(): pindah ke dashboard dan kirim pesan "flash session" (hanya tampil sekali di request berikutnya)
        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * Proses logout.
     */
    // method logout(): mengeluarkan user yang sedang login
    public function logout(Request $request)
    {
        // Auth::logout(): hapus status login user saat ini
        Auth::logout();

        // Hapus session lama
        // session()->invalidate(): hapus semua data session & buat ID session baru
        $request->session()->invalidate();
        // session()->regenerateToken(): buat ulang token CSRF (token keamanan form)
        $request->session()->regenerateToken();

        // redirect()->route('login'): arahkan kembali ke halaman login
        return redirect()->route('login');
    }
}
