<?php
// File Model User: representasi tabel "users" (M pada MVC).
// Dipakai untuk data admin yang login ke aplikasi (autentikasi).

// namespace: "alamat" class ini, sesuai folder app/Models. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // (dimatikan) interface MustVerifyEmail: aktifkan jika user wajib verifikasi email
use Database\Factories\UserFactory; // import class UserFactory: pembuat data dummy user (dipakai di seeder & test)
use Illuminate\Database\Eloquent\Attributes\Fillable; // import attribute Fillable: penanda kolom yang boleh diisi massal
use Illuminate\Database\Eloquent\Attributes\Hidden; // import attribute Hidden: penanda kolom yang disembunyikan saat diubah ke array/JSON
use Illuminate\Database\Eloquent\Factories\HasFactory; // import trait HasFactory: agar model bisa memakai factory
use Illuminate\Foundation\Auth\User as Authenticatable; // import class User bawaan Laravel, diberi alias (as) "Authenticatable": induk model yang bisa login
use Illuminate\Notifications\Notifiable; // import trait Notifiable: agar user bisa menerima notifikasi (email, dll.)

// Attribute #[Fillable]: daftar kolom yang boleh diisi massal lewat create()/update() (mass assignment)
#[Fillable(['name', 'email', 'password', 'photo'])]
// Attribute #[Hidden]: kolom rahasia yang tidak ikut ditampilkan saat model diubah ke array/JSON
#[Hidden(['password', 'remember_token'])]
// class User extends Authenticatable: deklarasi class (cetakan objek) User yang mewarisi (extends) fitur login dari Authenticatable
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // use trait: "menempelkan" kumpulan method siap pakai ke class ini.
    // HasFactory = bisa User::factory()->create(), Notifiable = bisa $user->notify(...)
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     * Casting = mengubah tipe data kolom secara otomatis saat dibaca/disimpan.
     *
     * @return array<string, string>
     */
    // method casts(): protected = hanya bisa dipakai di dalam class ini & turunannya; ": array" = tipe nilai kembalian (return type)
    protected function casts(): array
    {
        // return: mengembalikan array pasangan 'nama_kolom' => 'tipe cast'
        return [
            'email_verified_at' => 'datetime', // cast datetime: kolom dibaca sebagai objek tanggal-waktu (Carbon)
            'password' => 'hashed', // cast hashed: password otomatis dienkripsi (hash) saat disimpan
        ];
    }

    /**
     * Alamat (URL) foto profil.
     * Jika user belum upload foto, pakai foto default dari AdminLTE.
     */
    // method helper photoUrl(): public = bisa dipanggil dari luar, contoh di Blade: $user->photoUrl(). Mengembalikan string
    public function photoUrl(): string
    {
        // if: percabangan. $this = objek user ini sendiri; $this->photo = nilai kolom "photo" (isi = sudah upload foto)
        if ($this->photo) {
            // helper asset(): membuat URL lengkap ke folder public. Operator titik (.) = menyambung string
            return asset('storage/' . $this->photo);
        }

        // jika tidak ada foto: kembalikan URL gambar default bawaan template AdminLTE
        return asset('adminlte/assets/img/user2-160x160.jpg');
    }
}
