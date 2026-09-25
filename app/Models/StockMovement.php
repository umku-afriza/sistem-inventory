<?php
// File Model StockMovement: representasi tabel "stock_movements" (M pada MVC).
// Mencatat setiap transaksi stok: barang masuk (in) atau barang keluar (out).

// namespace: "alamat" class ini, sesuai folder app/Models. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Models;

use App\Enums\MovementType; // import enum MovementType: jenis transaksi (In / Out)
use Database\Factories\StockMovementFactory; // import class StockMovementFactory: pembuat data dummy transaksi
use Illuminate\Database\Eloquent\Attributes\Fillable; // import attribute Fillable: penanda kolom yang boleh diisi massal
use Illuminate\Database\Eloquent\Factories\HasFactory; // import trait HasFactory: agar model bisa memakai factory
use Illuminate\Database\Eloquent\Model; // import class Model: induk semua model Eloquent (ORM Laravel)
use Illuminate\Database\Eloquent\Relations\BelongsTo; // import class BelongsTo: tipe relasi many-to-one

// Attribute #[Fillable]: daftar kolom yang boleh diisi massal lewat create()/update() (mass assignment)
#[Fillable(['item_id', 'user_id', 'type', 'quantity', 'moved_at', 'note'])]
// class StockMovement extends Model: deklarasi class yang mewarisi fitur Eloquent. Tabel otomatis: "stock_movements"
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    // trait HasFactory: membuat model ini bisa dipakai dengan factory (data dummy), contoh StockMovement::factory()->create()
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     * "type" otomatis menjadi enum MovementType, "moved_at" menjadi objek Carbon.
     *
     * @return array<string, string>
     */
    // method casts(): mengatur konversi tipe data kolom secara otomatis (casting). protected = hanya dipakai di dalam class
    protected function casts(): array
    {
        return [
            'type' => MovementType::class, // cast enum: teks 'in'/'out' dari database otomatis jadi MovementType::In / ::Out
            'quantity' => 'integer', // cast integer: jumlah selalu angka bulat
            'moved_at' => 'date', // cast date: tanggal dibaca sebagai objek Carbon (bisa ->format('d/m/Y'))
        ];
    }

    /**
     * Bahan yang ditransaksikan.
     *
     * @return BelongsTo<Item, $this>
     */
    // relasi belongsTo (many-to-one): setiap transaksi milik satu bahan (lewat kolom item_id). Dipanggil sebagai $movement->item
    public function item(): BelongsTo
    {
        // $this = transaksi ini; Item::class = nama lengkap class model Item (App\Models\Item)
        return $this->belongsTo(Item::class);
    }

    /**
     * Admin yang mencatat transaksi.
     *
     * @return BelongsTo<User, $this>
     */
    // relasi belongsTo: setiap transaksi dicatat oleh satu user (lewat kolom user_id). Dipanggil sebagai $movement->user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
