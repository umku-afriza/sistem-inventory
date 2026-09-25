<?php
// File Model Item: representasi tabel "items" (data bahan/barang di gudang) (M pada MVC).
// Berisi relasi, casting, query scope, dan method bantu (helper) untuk bahan.

// namespace: "alamat" class ini, sesuai folder app/Models. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Models;

use Database\Factories\ItemFactory; // import class ItemFactory: pembuat data dummy bahan
use Illuminate\Database\Eloquent\Attributes\Fillable; // import attribute Fillable: penanda kolom yang boleh diisi massal
use Illuminate\Database\Eloquent\Attributes\Scope; // import attribute Scope: penanda method sebagai query scope
use Illuminate\Database\Eloquent\Builder; // import class Builder: objek penyusun query database
use Illuminate\Database\Eloquent\Factories\HasFactory; // import trait HasFactory: agar model bisa memakai factory
use Illuminate\Database\Eloquent\Model; // import class Model: induk semua model Eloquent (ORM Laravel)
use Illuminate\Database\Eloquent\Relations\BelongsTo; // import class BelongsTo: tipe relasi many-to-one
use Illuminate\Database\Eloquent\Relations\HasMany; // import class HasMany: tipe relasi one-to-many

// Attribute #[Fillable]: daftar kolom yang boleh diisi massal lewat create()/update() (mass assignment).
// Kolom di luar daftar ini akan diabaikan saat create()/update() (perlindungan keamanan)
#[Fillable(['category_id', 'code', 'name', 'unit', 'stock', 'min_stock', 'photo', 'description'])]
// class Item extends Model: deklarasi class Item yang mewarisi fitur Eloquent. Tabel otomatis: "items"
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    // trait HasFactory: membuat model ini bisa dipakai dengan factory (data dummy), contoh Item::factory()->create()
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     * Casting = mengubah tipe data kolom secara otomatis saat dibaca/disimpan.
     *
     * @return array<string, string>
     */
    // method casts(): protected = hanya dipakai di dalam class ini & turunannya; ": array" = return type (tipe nilai kembalian)
    protected function casts(): array
    {
        return [
            'stock' => 'integer', // cast integer: stok selalu dibaca sebagai angka bulat
            'min_stock' => 'integer', // cast integer: stok minimum juga angka bulat
        ];
    }

    /**
     * Relasi many-to-one: setiap bahan milik satu kategori.
     *
     * @return BelongsTo<Category, $this>
     */
    // PHPDoc @return BelongsTo<Category, $this>: keterangan untuk IDE bahwa relasi ini mengarah ke model Category
    // relasi belongsTo (many-to-one): setiap bahan dimiliki satu kategori. Dipanggil sebagai $item->category
    public function category(): BelongsTo
    {
        // belongsTo: memakai kolom category_id di tabel items untuk mencari kategorinya. $this = bahan ini
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi one-to-many: riwayat barang masuk/keluar bahan ini.
     *
     * @return HasMany<StockMovement, $this>
     */
    // relasi hasMany (one-to-many): satu bahan punya banyak transaksi stok. Dipanggil sebagai $item->stockMovements
    public function stockMovements(): HasMany
    {
        // hasMany: tabel stock_movements punya kolom item_id yang menunjuk ke bahan ini
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Query scope: hanya bahan yang stoknya sudah mencapai batas minimum.
     * Pemakaian: Item::lowStock()->get()
     *
     * @param  Builder<Item>  $query
     */
    // Attribute #[Scope]: menandai method di bawah sebagai query scope (filter query yang bisa dipakai ulang)
    #[Scope]
    // method scope lowStock(): parameter $query bertipe Builder (query yang sedang disusun); ": void" = tidak mengembalikan nilai
    protected function lowStock(Builder $query): void
    {
        // whereColumn: membandingkan dua kolom dalam satu baris, hasil SQL: WHERE stock <= min_stock
        $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Apakah stok bahan ini sudah menipis?
     */
    // method helper isLowStock(): mengembalikan bool (true/false), contoh: $item->isLowStock()
    public function isLowStock(): bool
    {
        // operator perbandingan <= : true jika stok lebih kecil atau sama dengan stok minimum
        return $this->stock <= $this->min_stock;
    }

    /**
     * Alamat (URL) foto bahan, atau null jika belum ada foto.
     */
    // method helper photoUrl(): return type "?string" = nullable, boleh berisi string ATAU null
    public function photoUrl(): ?string
    {
        // operator ternary (kondisi ? jika_benar : jika_salah): ada foto = buat URL dengan asset(), tidak ada = null.
        // Operator titik (.) = menyambung string
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
