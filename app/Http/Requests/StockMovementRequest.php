<?php
// File Form Request StockMovementRequest: tempat aturan validasi form transaksi stok.
// Dipakai di StockMovementController sebelum transaksi masuk/keluar dicatat.

// namespace: "alamat" class ini, sesuai folder app/Http/Requests. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Requests;

use App\Enums\MovementType; // import enum MovementType: pilihan jenis transaksi (in / out)
use Illuminate\Contracts\Validation\ValidationRule; // import interface ValidationRule: tipe aturan validasi (dipakai di PHPDoc)
use Illuminate\Foundation\Http\FormRequest; // import class FormRequest: induk semua Form Request Laravel
use Illuminate\Validation\Rule; // import class Rule: pembuat aturan validasi yang lebih kompleks (unique, enum, dll.)

/**
 * Validasi form transaksi stok (barang masuk / keluar).
 */
// class StockMovementRequest extends FormRequest: mewarisi fitur validasi otomatis. Jika gagal, user dikembalikan ke form beserta pesan error
class StockMovementRequest extends FormRequest
{
    /**
     * Semua admin yang sudah login boleh mencatat transaksi.
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
        return [
            'item_id' => ['required', 'exists:items,id'], // required = wajib diisi; exists:items,id = bahan harus benar-benar ada di tabel items
            // Rule::enum = nilai harus salah satu case di MovementType ("in" / "out")
            'type' => ['required', Rule::enum(MovementType::class)],
            'quantity' => ['required', 'integer', 'min:1'], // integer = angka bulat; min:1 = minimal 1 (tidak boleh 0/negatif)
            'moved_at' => ['required', 'date', 'before_or_equal:today'], // date = format tanggal valid; before_or_equal:today = tidak boleh tanggal di masa depan
            'note' => ['nullable', 'string', 'max:255'], // nullable = boleh kosong; string = teks; max:255 = maksimal 255 karakter
        ];
    }

    /**
     * Nama field yang tampil di pesan error.
     *
     * @return array<string, string>
     */
    // method attributes(): mengganti nama field di pesan error agar mudah dibaca, contoh "jumlah wajib diisi"
    public function attributes(): array
    {
        return [
            'item_id' => 'bahan', // item_id tampil sebagai "bahan"
            'type' => 'jenis transaksi',
            'quantity' => 'jumlah',
            'moved_at' => 'tanggal',
            'note' => 'keterangan',
        ];
    }
}
