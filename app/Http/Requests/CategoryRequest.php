<?php
// File Form Request CategoryRequest: tempat aturan validasi form kategori.
// Dipakai di CategoryController (store & update) sebelum data disimpan ke database.

// namespace: "alamat" class ini, sesuai folder app/Http/Requests. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule; // import interface ValidationRule: tipe aturan validasi (dipakai di PHPDoc)
use Illuminate\Foundation\Http\FormRequest; // import class FormRequest: induk semua Form Request Laravel
use Illuminate\Validation\Rule; // import class Rule: pembuat aturan validasi yang lebih kompleks (unique, enum, dll.)

/**
 * Validasi form kategori (dipakai untuk tambah & edit).
 * Form Request memindahkan aturan validasi keluar dari controller agar controller tetap ringkas.
 */
// class CategoryRequest extends FormRequest: mewarisi fitur validasi otomatis. Jika gagal, user dikembalikan ke form beserta pesan error
class CategoryRequest extends FormRequest
{
    /**
     * Semua admin yang sudah login boleh mengelola kategori.
     */
    // method authorize(): siapa yang boleh mengirim form ini. true = semua user login boleh (false = ditolak 403)
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
        return [
            // Aturan "name": required = wajib diisi, string = harus teks, max:100 = maksimal 100 karakter,
            // Rule::unique('categories') = nama tidak boleh sama dengan yang sudah ada di tabel categories.
            // ->ignore(...): saat edit, $this->route('category') berisi kategori yang sedang diedit (dari parameter route {category}),
            // sehingga nama miliknya sendiri tidak dianggap duplikat
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($this->route('category'))],
            // Aturan "description": nullable = boleh kosong, string = teks, max:500 = maksimal 500 karakter
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Nama field yang tampil di pesan error.
     *
     * @return array<string, string>
     */
    // method attributes(): mengganti nama field di pesan error, contoh "nama kategori wajib diisi" (bukan "name wajib diisi")
    public function attributes(): array
    {
        return [
            'name' => 'nama kategori', // field name tampil sebagai "nama kategori"
            'description' => 'deskripsi', // field description tampil sebagai "deskripsi"
        ];
    }
}
