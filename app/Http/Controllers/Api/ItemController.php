<?php
// File ini: Api\ItemController, controller REST API untuk data bahan (hanya baca), mengembalikan JSON bukan halaman HTML.
// Peran di MVC: "C" (Controller) yang memakai Model Item; "View"-nya diganti API Resource (ItemResource) yang membentuk JSON.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers/Api. Beda namespace dengan ItemController web, jadi nama sama tidak bentrok.
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // import class induk Controller (beda folder/namespace, jadi harus di-import)
use App\Http\Resources\ItemResource; // import API Resource: mengatur bentuk JSON satu bahan
use App\Models\Item; // import Model Item: tabel "items" (bahan)
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim client (query string, dll.)
use Illuminate\Http\Resources\Json\AnonymousResourceCollection; // import class AnonymousResourceCollection: return type kumpulan resource

/**
 * REST API bahan (hanya baca).
 * Coba di browser/Postman: GET /api/items dan GET /api/items/1
 */
// class + extends (pewarisan/inheritance): Api\ItemController mewarisi semua kemampuan class Controller
class ItemController extends Controller
{
    /**
     * GET /api/items - Daftar bahan (dengan pagination).
     * Filter opsional: ?search=spidol&low_stock=1
     */
    // method index(): daftar data. Return type ": AnonymousResourceCollection" = kumpulan ItemResource dalam bentuk JSON
    public function index(Request $request): AnonymousResourceCollection
    {
        // Item::query(): mulai membangun query Eloquent ke tabel items
        $items = Item::query()
            // with('category'): eager loading kategori, mencegah N+1 query
            ->with('category')
            // when() + arrow function "fn (...) => ...": filter nama hanya jika ?search= diisi; "{$search}" = string interpolation
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            // query scope lowStock() dari model Item, dipakai jika ?low_stock=1
            ->when($request->boolean('low_stock'), fn ($query) => $query->lowStock())
            // orderBy('name'): urutkan nama A-Z
            ->orderBy('name')
            // paginate(10): 10 data per halaman (JSON otomatis berisi "links" & "meta" pagination)
            ->paginate(10)
            // withQueryString(): link halaman berikutnya tetap membawa filter
            ->withQueryString();

        // ItemResource::collection(): ubah setiap Item menjadi JSON sesuai format di ItemResource
        return ItemResource::collection($items);
    }

    /**
     * GET /api/items/{item} - Detail satu bahan.
     */
    // Route Model Binding: Laravel otomatis mencari Item berdasarkan {item} di URL, jika tidak ada -> 404 (JSON). Return type ": ItemResource"
    public function show(Item $item): ItemResource
    {
        // new ItemResource(...): buat objek resource untuk satu bahan; load('category') memuat relasi kategori dulu
        return new ItemResource($item->load('category'));
    }
}
