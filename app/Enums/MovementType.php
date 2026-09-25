<?php
// File Enum MovementType: daftar pilihan tetap untuk jenis transaksi stok (masuk / keluar).
// Dipakai di model StockMovement (casting) dan validasi form (Rule::enum).

// namespace: "alamat" enum ini, sesuai folder app/Enums. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Enums;

/**
 * Jenis transaksi stok.
 * Enum membuat nilai "in"/"out" tidak bisa salah ketik di seluruh aplikasi.
 */
// enum MovementType: string = backed enum, yaitu setiap pilihan (case) punya nilai string yang disimpan di database
enum MovementType: string
{
    // Enum case: pilihan tetap, nilai 'in' yang disimpan di database. Dipanggil MovementType::In
    case In = 'in';
    // Enum case: pilihan tetap, nilai 'out' yang disimpan di database. Dipanggil MovementType::Out
    case Out = 'out';

    /**
     * Teks yang ditampilkan ke pengguna.
     */
    // method label(): enum juga boleh punya method. Contoh: $movement->type->label() menghasilkan 'Masuk'
    public function label(): string
    {
        // ekspresi match: seperti switch yang lebih ringkas, mengembalikan nilai sesuai case yang cocok.
        // $this = case enum saat ini; self:: = menunjuk ke enum ini sendiri (MovementType)
        return match ($this) {
            self::In => 'Masuk', // jika case In, tampilkan 'Masuk'
            self::Out => 'Keluar', // jika case Out, tampilkan 'Keluar'
        };
    }

    /**
     * Warna badge Bootstrap.
     */
    // method color(): nama warna Bootstrap untuk badge, contoh di Blade: class="badge text-bg-{{ $type->color() }}"
    public function color(): string
    {
        // match: pilih warna sesuai case
        return match ($this) {
            self::In => 'success', // 'success' = hijau, untuk barang masuk
            self::Out => 'danger', // 'danger' = merah, untuk barang keluar
        };
    }
}
