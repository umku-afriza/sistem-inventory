<?php
// File Form Request ItemRequest: tempat aturan validasi form bahan (item).
// Dipakai di ItemController (store & update) agar controller tidak penuh kode validasi.

// namespace: "alamat" class ini, sesuai folder app/Http/Requests. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule; // import interface ValidationRule: tipe aturan validasi (dipakai di PHPDoc)
use Illuminate\Foundation\Http\FormRequest; // import class FormRequest: induk semua Form Request Laravel
use Illuminate\Validation\Rule; // import class Rule: pembuat aturan validasi yang lebih kompleks (unique, enum, dll.)

/**
 * Validasi form bahan (dipakai untuk tambah & edit).
 */
// class ItemRequest extends FormRequest: mewarisi fitur validasi otomatis. Jika gagal, user dikembalikan ke form beserta pesan error
class ItemRequest extends FormRequest
{
    /**
     * Semua admin yang sudah login boleh mengelola bahan.
     */
    // method authorize(): siapa yang boleh mengirim form ini. true = semua user login boleh
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    // method rules(): daftar aturan validasi. Format: 'nama_field' => [aturan1, aturan2, ...]
    public function rules(): array
    {
        // variabel $rules: aturan disimpan dulu di variabel, karena nanti bisa ditambah (lihat if di bawah)
        $rules = [
            'category_id' => ['required', 'exists:categories,id'], // required = wajib diisi; exists:categories,id = nilainya harus ada di kolom id tabel categories
            // Rule::unique('items') = kode bahan tidak boleh kembar di tabel items;
            // ->ignore($this->route('item')) = saat edit, abaikan bahan yang sedang diedit (dari parameter route {item})
            'code' => ['required', 'string', 'max:20', Rule::unique('items')->ignore($this->route('item'))],
            'name' => ['required', 'string', 'max:255'], // string = harus teks; max:255 = maksimal 255 karakter
            'unit' => ['required', 'string', 'max:20'], // satuan, contoh: kg, liter, pcs
            'min_stock' => ['required', 'integer', 'min:0'], // integer = angka bulat; min:0 = tidak boleh negatif
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // nullable = boleh kosong; image = harus gambar; mimes = hanya jpg/jpeg/png; max:2048 = maks 2048 KB (2 MB)
            'description' => ['nullable', 'string', 'max:1000'], // boleh kosong, teks, maksimal 1000 karakter
        ];

        // Stok awal hanya diisi saat tambah bahan.
        // Setelah itu, stok hanya boleh berubah lewat menu Transaksi Stok.
        // $this->isMethod('post'): cek apakah request memakai HTTP method POST (= form tambah). Form edit memakai PUT/PATCH
        if ($this->isMethod('post')) {
            // menambah aturan baru ke array $rules dengan key 'stock'
            $rules['stock'] = ['required', 'integer', 'min:0'];
        }

        // return: kirim semua aturan ke Laravel untuk diperiksa
        return $rules;
    }

    /**
     * Nama field yang tampil di pesan error.
     *
     * @return array<string, string>
     */
    // method attributes(): mengganti nama field di pesan error agar mudah dibaca, contoh "kode bahan wajib diisi"
    public function attributes(): array
    {
        return [
            'category_id' => 'kategori', // category_id tampil sebagai "kategori"
            'code' => 'kode bahan',
            'name' => 'nama bahan',
            'unit' => 'satuan',
            'stock' => 'stok awal',
            'min_stock' => 'stok minimum',
            'photo' => 'foto',
            'description' => 'deskripsi',
        ];
    }
}
