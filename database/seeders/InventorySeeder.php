<?php
// File InventorySeeder: mengisi data dummy kategori, bahan, dan riwayat transaksi stok.
// Seeder: mengisi data awal ke database. Dipanggil dari DatabaseSeeder

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/seeders)
namespace Database\Seeders;

// use: import class dari namespace lain
use App\Enums\MovementType; // Enum jenis transaksi: In (masuk) / Out (keluar)
use App\Models\Category; // model Category (tabel categories)
use App\Models\Item; // model Item (tabel items)
use App\Models\StockMovement; // model StockMovement (tabel stock_movements)
use App\Models\User; // model User (tabel users)
use Illuminate\Database\Seeder; // class induk semua seeder
use Illuminate\Support\Carbon; // Carbon: library untuk mengolah tanggal & waktu
use Illuminate\Support\Collection; // Collection: "array plus" milik Laravel dengan banyak method (map, sort, random, dll)

/**
 * Data dummy kategori, bahan, dan riwayat stok 6 bulan terakhir.
 * Cukup banyak untuk demo pencarian, filter, pagination, dan laporan.
 *
 * Jalankan sendiri dengan: php artisan db:seed --class=InventorySeeder
 * Atau reset semua data: php artisan migrate:fresh --seed
 */
// class InventorySeeder extends Seeder: turunan (inheritance) dari class Seeder
class InventorySeeder extends Seeder
{
    /**
     * [nama kategori => [prefix kode, deskripsi, [[nama bahan, satuan, stok minimum, dibuat menipis?], ...]]]
     *
     * private const: konstanta (nilai tetap, tidak bisa diubah) yang hanya bisa dipakai di dalam class ini.
     * Isinya array bertingkat (nested array). Contoh baris: ['Spidol Whiteboard Biru', 'pcs', 5, true]
     * = nama bahan 'Spidol Whiteboard Biru', satuan 'pcs', stok minimum 5, true = dibuat stoknya menipis.
     *
     * @var array<string, array{0: string, 1: string, 2: list<array{0: string, 1: string, 2: int, 3: bool}>}>
     */
    private const CATEGORIES = [
        // Struktur tiap kategori: 'Nama Kategori' => ['PREFIX KODE', 'deskripsi', [daftar bahan]]
        'Alat Tulis' => ['ATK', 'Perlengkapan tulis untuk peserta dan mentor', [
            ['Spidol Whiteboard Hitam', 'pcs', 10, false],
            ['Spidol Whiteboard Merah', 'pcs', 5, false],
            ['Spidol Whiteboard Biru', 'pcs', 5, true],
            ['Penghapus Whiteboard', 'pcs', 3, false],
            ['Kertas HVS A4', 'rim', 5, true],
            ['Kertas HVS F4', 'rim', 3, false],
            ['Sticky Notes', 'pak', 10, false],
            ['Pulpen Hitam', 'box', 2, false],
            ['Pensil 2B', 'box', 2, false],
            ['Buku Tulis', 'pcs', 20, false],
            ['Map Plastik', 'pcs', 15, false],
            ['Stapler', 'pcs', 2, false],
            ['Isi Stapler', 'box', 3, true],
        ]],
        'Elektronik' => ['ELK', 'Peralatan elektronik pendukung kelas', [
            ['Kabel HDMI 2 Meter', 'pcs', 2, false],
            ['Stop Kontak 4 Lubang', 'pcs', 3, true],
            ['Pointer Presentasi', 'unit', 2, false],
            ['Adaptor USB-C', 'unit', 2, false],
            ['Baterai AA', 'pak', 4, false],
            ['Speaker Bluetooth', 'unit', 1, false],
            ['Microphone Wireless', 'unit', 1, false],
        ]],
        'Jaringan' => ['JRG', 'Perangkat jaringan dan internet kelas', [
            ['Router WiFi', 'unit', 1, false],
            ['Kabel LAN Cat6 5 Meter', 'pcs', 5, false],
            ['Switch Hub 8 Port', 'unit', 1, false],
            ['Konektor RJ45', 'pak', 2, true],
        ]],
        'Komputer' => ['KMP', 'Aksesoris dan perangkat komputer', [
            ['Mouse Wireless', 'unit', 5, false],
            ['Keyboard USB', 'unit', 3, false],
            ['Flashdisk 32GB', 'pcs', 5, false],
            ['Headset', 'unit', 3, true],
            ['Webcam HD', 'unit', 1, false],
            ['Laptop Cadangan', 'unit', 1, false],
        ]],
        'Konsumsi' => ['KSM', 'Makanan dan minuman untuk peserta', [
            ['Air Mineral Botol', 'dus', 5, false],
            ['Kopi Sachet', 'pak', 5, true],
            ['Teh Celup', 'box', 3, false],
            ['Gula Pasir', 'kg', 2, false],
            ['Snack Box', 'box', 20, false],
            ['Gelas Plastik', 'pak', 3, false],
        ]],
        'Merchandise' => ['MRC', 'Souvenir dan atribut bootcamp', [
            ['Kaos Bootcamp Ukuran S', 'pcs', 5, false],
            ['Kaos Bootcamp Ukuran M', 'pcs', 10, false],
            ['Kaos Bootcamp Ukuran L', 'pcs', 10, true],
            ['Kaos Bootcamp Ukuran XL', 'pcs', 5, false],
            ['Tote Bag', 'pcs', 10, false],
            ['Tumbler', 'pcs', 10, false],
            ['Stiker Laptop', 'lembar', 20, false],
            ['ID Card & Lanyard', 'pcs', 20, false],
        ]],
        'Perlengkapan Kelas' => ['KLS', 'Peralatan tetap ruang kelas', [
            ['Proyektor', 'unit', 1, false],
            ['Layar Proyektor', 'unit', 1, false],
            ['Whiteboard Portable', 'unit', 1, false],
            ['Kursi Lipat', 'unit', 10, false],
            ['Meja Lipat', 'unit', 5, false],
        ]],
        'Buku & Modul' => ['BKM', 'Modul cetak dan buku referensi', [
            ['Modul Laravel Dasar', 'eksemplar', 10, false],
            ['Modul Laravel Lanjut', 'eksemplar', 10, true],
            ['Modul HTML & CSS', 'eksemplar', 10, false],
            ['Modul JavaScript', 'eksemplar', 10, false],
            ['Buku Clean Code', 'eksemplar', 2, false],
        ]],
        'Kebersihan' => ['KBR', 'Perlengkapan kebersihan ruangan', [
            ['Tisu', 'pak', 5, false],
            ['Sabun Cuci Tangan', 'botol', 3, false],
            ['Hand Sanitizer', 'botol', 5, true],
            ['Kantong Sampah', 'pak', 3, false],
            ['Pembersih Lantai', 'botol', 2, false],
        ]],
        'Kesehatan' => ['P3K', 'Perlengkapan P3K', [
            ['Kotak P3K', 'unit', 1, false],
            ['Masker', 'box', 2, false],
            ['Plester Luka', 'box', 1, false],
        ]],
        'Dokumentasi' => ['DOK', 'Peralatan foto dan video kegiatan', [
            ['Kartu Memori 64GB', 'pcs', 2, false],
            ['Tripod', 'unit', 1, false],
            ['Ring Light', 'unit', 1, false],
        ]],
        'Hadiah' => ['HDH', 'Hadiah untuk peserta terbaik', [
            ['Voucher Belanja', 'lembar', 5, false],
            ['Sertifikat Kosong', 'lembar', 30, true],
            ['Plakat Juara', 'pcs', 3, false],
        ]],
        'Dekorasi' => ['DEK', 'Dekorasi acara pembukaan & demo day', [
            ['Banner Bootcamp', 'pcs', 1, false],
            ['X-Banner', 'pcs', 1, false],
            ['Balon', 'pak', 2, false],
        ]],
    ];

    /** Keterangan acak untuk barang keluar. */
    // private const OUT_NOTES: konstanta berisi daftar teks catatan, nanti dipilih salah satu secara acak
    private const OUT_NOTES = [
        'Kelas Laravel Batch 5', 'Kelas Laravel Batch 6', 'Kelas Web Dasar', 'Workshop UI/UX',
        'Kelas Flutter', 'Demo Day', 'Career Day', 'Kebutuhan kantor', 'Rusak / hilang',
    ];

    /** Keterangan acak untuk barang masuk. */
    // private const IN_NOTES: konstanta berisi daftar teks catatan untuk transaksi barang masuk
    private const IN_NOTES = [
        'Restock dari supplier', 'Pembelian bulanan', 'Donasi sponsor', 'Pengadaan batch baru',
    ];

    // run(): method yang otomatis dijalankan saat seeder dipanggil
    public function run(): void
    {
        // fake()->seed(2026): Seed tetap = data acak yang dihasilkan selalu sama setiap kali seeder dijalankan
        fake()->seed(2026);

        // pluck('id'): ambil hanya kolom id dari semua user, hasilnya Collection. Dipakai untuk memilih admin pencatat secara acak
        $userIds = User::pluck('id');
        // $movements: array kosong penampung semua transaksi, nanti disimpan sekaligus di akhir
        $movements = [];

        // foreach + destructuring [$a, $b, $c]: perulangan tiap kategori, isi array langsung "dibongkar" ke 3 variabel.
        // self::CATEGORIES = memanggil konstanta milik class ini. $categoryName = key (nama kategori)
        foreach (self::CATEGORIES as $categoryName => [$prefix, $description, $items]) {
            // firstOrCreate: cari kategori berdasarkan nama; jika belum ada, buat baru dengan deskripsinya
            $category = Category::firstOrCreate(['name' => $categoryName], ['description' => $description]);

            // foreach bertingkat (nested loop): perulangan tiap bahan di kategori ini. $index = urutan mulai 0
            foreach ($items as $index => [$name, $unit, $minStock, $makeLowStock]) {
                // $category->items()->firstOrCreate: lewat relasi hasMany, category_id otomatis terisi.
                // sprintf('%s-%03d', ...): format teks; %s = teks prefix, %03d = angka 3 digit diawali nol (001, 002, ...)
                $item = $category->items()->firstOrCreate(
                    ['code' => sprintf('%s-%03d', $prefix, $index + 1)], // contoh: ATK-001
                    ['name' => $name, 'unit' => $unit, 'min_stock' => $minStock],
                );

                // Seeder dijalankan ulang? Bahan sudah ada, jangan tambah riwayat lagi
                // wasRecentlyCreated: bernilai true jika data BARU saja dibuat oleh firstOrCreate (bukan data lama)
                // continue: lewati sisa perulangan ini, lanjut ke bahan berikutnya
                if (! $item->wasRecentlyCreated) {
                    continue;
                }

                // Destructuring: method mengembalikan array [stok, riwayat], langsung dibongkar ke $stock dan $history
                [$stock, $history] = $this->simulateHistory($item, $makeLowStock, $userIds);

                // Stok akhir bahan = hasil seluruh transaksinya, sehingga data selalu konsisten
                $item->update(['stock' => $stock]);
                // array_push + spread operator (...): masukkan SEMUA isi $history satu per satu ke $movements
                array_push($movements, ...$history);
            }
        }

        // Urutkan berdasarkan tanggal agar id transaksi sesuai urutan waktu
        // (dashboard menampilkan "Transaksi Terakhir" berdasarkan id)
        // usort: mengurutkan array dengan fungsi pembanding. <=> (spaceship operator) menghasilkan -1, 0, atau 1
        usort($movements, fn (array $a, array $b) => $a['moved_at'] <=> $b['moved_at']);

        // insert() menyimpan banyak baris dalam satu query, jauh lebih cepat daripada create() satu per satu.
        // Kekurangannya: cast & timestamps tidak otomatis, jadi nilainya diisi manual.
        // array_chunk($movements, 200): memecah array menjadi potongan berisi maks 200 baris agar query tidak terlalu besar
        foreach (array_chunk($movements, 200) as $chunk) {
            // Model::insert: simpan satu potongan (maks 200 baris) sekaligus dalam satu query
            StockMovement::insert($chunk);
        }
    }

    /**
     * Buat riwayat transaksi acak selama 6 bulan terakhir tanpa membuat stok minus.
     *
     * @param  Collection<int, int>  $userIds
     * @return array{0: int, 1: list<array<string, mixed>>}
     *
     * private function: method yang hanya bisa dipanggil dari dalam class ini. Mengembalikan [stok akhir, daftar riwayat]
     */
    private function simulateHistory(Item $item, bool $makeLowStock, Collection $userIds): array
    {
        $stock = 0; // stok berjalan, dimulai dari 0
        $history = []; // penampung riwayat transaksi bahan ini

        // Closure (fungsi tanpa nama) disimpan di variabel $record, dipanggil untuk mencatat satu transaksi.
        // use (...): membawa variabel dari luar ke dalam closure. Tanda & (reference) = variabel aslinya yang diubah,
        // bukan salinannya, sehingga perubahan $stock & $history di dalam closure ikut terlihat di luar
        $record = function (MovementType $type, int $quantity, Carbon $date, ?string $note) use (&$stock, &$history, $item, $userIds) {
            // Ternary operator (kondisi ? A : B): barang masuk menambah stok, barang keluar mengurangi stok
            $stock += $type === MovementType::In ? $quantity : -$quantity;

            // $history[] = [...]: menambahkan satu baris baru di akhir array. Timestamps diisi manual karena memakai insert()
            $history[] = [
                'item_id' => $item->id,
                'user_id' => $userIds->random(), // random(): pilih satu id user secara acak
                'type' => $type->value, // ->value: nilai asli enum, yaitu "in" atau "out"
                'quantity' => $quantity,
                'moved_at' => $date->toDateString(), // toDateString(): ubah tanggal jadi teks format 2026-09-24
                'note' => $note,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        };

        // 1. Stok awal sekitar 6 bulan lalu
        // $record(...): memanggil closure. today()->subMonths(6)->addDays(...): hari ini dikurangi 6 bulan, ditambah 0-7 hari acak
        $record(
            MovementType::In,
            fake()->numberBetween($item->min_stock * 4, $item->min_stock * 10),
            today()->subMonths(6)->addDays(fake()->numberBetween(0, 7)),
            'Stok awal',
        );

        // 2. Beberapa transaksi acak setelahnya, diurutkan dari tanggal terlama
        // collect(range(...)): membuat Collection berisi 5-18 angka (jumlah transaksi acak)
        // ->map(): ubah setiap angka menjadi tanggal acak dalam 170 hari terakhir (Carbon, jam direset ke 00:00 oleh startOfDay)
        // ->sort(): urutkan tanggal dari yang terlama
        $dates = collect(range(1, fake()->numberBetween(5, 18)))
            ->map(fn () => Carbon::instance(fake()->dateTimeBetween('-170 days'))->startOfDay())
            ->sort();

        // foreach: perulangan tiap tanggal transaksi
        foreach ($dates as $date) {
            // if: jika masih ada stok DAN peluang 70% (fake()->boolean(70)), catat barang keluar
            if ($stock > 0 && fake()->boolean(70)) {
                // Barang keluar maks 1/3 stok (intdiv = pembagian bulat), max(1, ...) = minimal 1.
                // fake()->optional(0.85): 85% ada catatan, 15% catatan kosong (null)
                $record(MovementType::Out, fake()->numberBetween(1, max(1, intdiv($stock, 3))), $date, fake()->optional(0.85)->randomElement(self::OUT_NOTES));
            } else {
                // else: selain itu, catat barang masuk (restock) dengan catatan acak dari IN_NOTES
                $record(MovementType::In, fake()->numberBetween($item->min_stock * 2, $item->min_stock * 5), $date, fake()->randomElement(self::IN_NOTES));
            }
        }

        // 3. Bahan yang ditandai "menipis": habiskan stok hingga di bawah batas minimum
        // if: hanya jika bahan ditandai menipis DAN stoknya masih di atas min_stock
        if ($makeLowStock && $stock > $item->min_stock) {
            // Keluarkan barang sebanyak (stok - angka acak 0..min_stock), sehingga sisa stok <= min_stock. today() = hari ini
            $record(MovementType::Out, $stock - fake()->numberBetween(0, $item->min_stock), today(), 'Kelas Laravel Batch 7');
        }

        // return: kembalikan stok akhir dan seluruh riwayat dalam bentuk array
        return [$stock, $history];
    }
}
