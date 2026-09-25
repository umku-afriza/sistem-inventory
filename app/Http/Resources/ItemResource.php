<?php
// File API Resource ItemResource: "penerjemah" model Item menjadi data JSON untuk API.
// Dipakai di controller API, contoh: return ItemResource::collection($items);

// namespace: "alamat" class ini, sesuai folder app/Http/Resources. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Resources;

use App\Models\Item; // import model Item: dipakai di PHPDoc @mixin di bawah
use Illuminate\Http\Request; // import class Request: data permintaan (request) HTTP dari klien
use Illuminate\Http\Resources\Json\JsonResource; // import class JsonResource: induk semua API Resource Laravel

/**
 * Mengatur bentuk JSON bahan yang dikirim lewat API.
 * Dengan Resource, struktur tabel database tidak harus sama dengan data yang dilihat klien.
 *
 * @mixin Item
 */
// PHPDoc @mixin Item: memberi tahu IDE bahwa $this di class ini bisa dipakai seperti objek Item ($this->name, dll.)
// class ItemResource extends JsonResource: mewarisi kemampuan mengubah model menjadi JSON
class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // method toArray(): menentukan isi JSON. Parameter $request = request saat ini; hasil array otomatis diubah Laravel jadi JSON
    public function toArray(Request $request): array
    {
        // return array: 'key_di_json' => nilai. $this = bahan (Item) yang sedang diubah
        return [
            'id' => $this->id, // $this->id = kolom id milik bahan ini
            'code' => $this->code,
            'name' => $this->name,
            // whenLoaded: kategori hanya ditampilkan jika relasinya sudah di-load di controller
            // (misalnya dengan Item::with('category')). fn () => ... = arrow function (fungsi singkat) yang mengambil nama kategori
            'category' => $this->whenLoaded('category', fn () => $this->category->name),
            'unit' => $this->unit,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'is_low_stock' => $this->isLowStock(), // memanggil method helper di model Item: true/false
            'photo_url' => $this->photoUrl(), // URL foto lengkap (atau null)
            'description' => $this->description,
            // operator nullsafe (?->): jika updated_at null, hasilnya null (tidak error).
            // toIso8601String() = format tanggal standar internasional, contoh 2026-01-31T10:00:00+07:00
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
