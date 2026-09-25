<?php
// File Model Category: representasi tabel "categories" di database (M pada MVC).
// Satu kategori mengelompokkan banyak bahan (item).

// namespace: "alamat" class ini, sesuai folder app/Models. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Models;

use Database\Factories\CategoryFactory; // import class CategoryFactory: pembuat data dummy kategori
use Illuminate\Database\Eloquent\Attributes\Fillable; // import attribute Fillable: penanda kolom yang boleh diisi massal
use Illuminate\Database\Eloquent\Factories\HasFactory; // import trait HasFactory: agar model bisa memakai factory
use Illuminate\Database\Eloquent\Model; // import class Model: induk semua model Eloquent (ORM Laravel)
use Illuminate\Database\Eloquent\Relations\HasMany; // import class HasMany: tipe relasi one-to-many

// Attribute #[Fillable]: daftar kolom yang boleh diisi massal lewat create()/update() (mass assignment)
#[Fillable(['name', 'description'])]
// class Category extends Model: deklarasi class Category yang mewarisi (extends) semua fitur Eloquent dari Model.
// Nama tabel otomatis ditebak Laravel: Category -> "categories" (jamak)
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    // trait HasFactory: membuat model ini bisa dipakai dengan factory (data dummy), contoh Category::factory()->create()
    use HasFactory;

    /**
     * Relasi one-to-many: satu kategori punya banyak bahan.
     *
     * @return HasMany<Item, $this>
     */
    // PHPDoc @return HasMany<Item, $this>: keterangan untuk editor/IDE bahwa relasi ini berisi model Item milik kategori ini
    // method relasi items(): dipanggil sebagai $category->items (hasil: kumpulan/collection Item). ": HasMany" = return type
    public function items(): HasMany
    {
        // relasi hasMany: tabel items punya kolom category_id yang menunjuk ke kategori ini.
        // $this = objek kategori ini; Item::class = nama lengkap class Item (App\Models\Item)
        return $this->hasMany(Item::class);
    }
}
