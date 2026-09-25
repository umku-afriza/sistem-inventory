<?php
// File ini: ReportController, controller untuk laporan stok per periode dan export ke file CSV.
// Peran di MVC: "C" (Controller) yang mengolah data Model Item lalu menampilkan View resources/views/reports atau mengirim file.

// namespace: "alamat" class ini, sesuai folder app/Http/Controllers. Dipakai Composer (autoload) untuk menemukan file ini.
namespace App\Http\Controllers;

use App\Enums\MovementType; // import Enum MovementType: jenis transaksi (In = masuk, Out = keluar)
use App\Models\Item; // import Model Item: tabel "items" (bahan)
use Illuminate\Database\Eloquent\Builder; // import class Builder: objek query Eloquent (dipakai sebagai type hint)
use Illuminate\Database\Eloquent\Collection; // import class Collection: kumpulan hasil model (return type reportItems())
use Illuminate\Http\Request; // import class Request: berisi semua data yang dikirim browser (input form, query string, file)
use Illuminate\Support\Carbon; // import class Carbon: objek tanggal & waktu dengan banyak fungsi bantu
use Illuminate\View\View; // import class View: return type untuk method yang mengembalikan halaman
use Symfony\Component\HttpFoundation\StreamedResponse; // import class StreamedResponse: response berupa file yang dikirim bertahap (download)

/**
 * Laporan Stok per periode + export CSV.
 * Materi: agregasi (withSum), validasi query string, response download.
 */
// class + extends (pewarisan/inheritance): ReportController mewarisi semua kemampuan class Controller
class ReportController extends Controller
{
    /**
     * Tampilkan laporan stok.
     */
    // method index(): tampilkan halaman laporan. Return type ": View" = wajib mengembalikan halaman
    public function index(Request $request): View
    {
        // array destructuring "[$a, $b] = ...": pecah array hasil period() menjadi 2 variabel sekaligus.
        // $this->period(): memanggil method lain di class yang sama ($this = objek class ini)
        [$startDate, $endDate] = $this->period($request);

        // ambil data laporan untuk periode tersebut
        $items = $this->reportItems($startDate, $endDate);

        // compact(): kirim variabel ke view resources/views/reports/index.blade.php
        return view('reports.index', compact('items', 'startDate', 'endDate'));
    }

    /**
     * Unduh laporan dalam format CSV (bisa dibuka di Excel).
     */
    // method export(): return type ": StreamedResponse" = mengembalikan file download, bukan halaman
    public function export(Request $request): StreamedResponse
    {
        // periode & data sama seperti di index()
        [$startDate, $endDate] = $this->period($request);

        $items = $this->reportItems($startDate, $endDate);

        // string interpolation "{$startDate->format('Ymd')}": sisipkan tanggal berformat 20260901 ke nama file
        $fileName = "laporan-stok-{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.csv";

        // streamDownload menulis isi file langsung ke browser tanpa menyimpannya di server
        // response()->streamDownload(closure, namaFile, header): closure "use ($items)" berisi proses penulisan isi file
        return response()->streamDownload(function () use ($items) {
            // fopen('php://output', 'w'): buka "file" khusus yang langsung mengalir ke browser, mode tulis (w)
            $file = fopen('php://output', 'w');

            // fputcsv(): tulis satu baris CSV; baris pertama = judul kolom (header)
            fputcsv($file, ['Kode', 'Nama Bahan', 'Kategori', 'Satuan', 'Masuk', 'Keluar', 'Stok Saat Ini']);

            // foreach: perulangan untuk setiap bahan di $items
            foreach ($items as $item) {
                // tulis satu baris data per bahan
                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->category->name, // nama kategori lewat relasi belongsTo
                    $item->unit,
                    $item->total_in ?? 0, // "??" (null coalescing): jika null (tidak ada transaksi), tulis 0
                    $item->total_out ?? 0,
                    $item->stock,
                ]);
            }

            // fclose(): tutup file setelah selesai ditulis
            fclose($file);
        // argumen ke-2 = nama file download, ke-3 = header HTTP (Content-Type text/csv = jenis file CSV)
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Ambil periode laporan dari query string. Default: bulan ini.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    // method private: hanya bisa dipanggil dari dalam class ini (method bantu / helper). Return type ": array"
    private function period(Request $request): array
    {
        // validate(): validasi query string ?start_date=...&end_date=...
        $request->validate([
            'start_date' => ['nullable', 'date'], // boleh kosong, jika diisi harus format tanggal
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'], // tidak boleh sebelum start_date
        ]);

        // kembalikan array 2 elemen: [tanggal awal, tanggal akhir]
        return [
            // $request->date(): ubah input jadi objek Carbon (null jika kosong); "??" -> default awal bulan ini
            $request->date('start_date') ?? now()->startOfMonth(),
            // default akhir bulan ini; now() = helper tanggal & jam sekarang
            $request->date('end_date') ?? now()->endOfMonth(),
        ];
    }

    /**
     * Semua bahan beserta total masuk & keluar dalam periode tertentu.
     *
     * @return Collection<int, Item>
     */
    // method private reportItems(): parameter bertipe Carbon, return type ": Collection" (kumpulan model Item)
    private function reportItems(Carbon $startDate, Carbon $endDate): Collection
    {
        // Filter periode yang dipakai oleh kedua perhitungan withSum di bawah
        // $inPeriod = fn (...) => ...: arrow function disimpan di variabel agar bisa dipakai ulang; otomatis bisa memakai $startDate & $endDate
        $inPeriod = fn (Builder $query) => $query
            // whereDate(): bandingkan bagian tanggal saja, mulai dari $startDate
            ->whereDate('moved_at', '>=', $startDate)
            // sampai $endDate
            ->whereDate('moved_at', '<=', $endDate);

        // Item::query(): mulai membangun query Eloquent ke tabel items
        return Item::query()
            // with('category'): eager loading kategori, mencegah N+1 query
            ->with('category')
            // withSum menambahkan kolom hasil SUM(quantity), dinamai sesuai alias "as ..."
            // $inPeriod($query): panggil arrow function filter periode, lalu tambah where('type', In) = hanya barang masuk
            ->withSum(['stockMovements as total_in' => fn ($query) => $inPeriod($query)->where('type', MovementType::In)], 'quantity')
            // sama seperti di atas, tetapi untuk barang keluar -> kolom total_out
            ->withSum(['stockMovements as total_out' => fn ($query) => $inPeriod($query)->where('type', MovementType::Out)], 'quantity')
            // orderBy('name'): urutkan nama A-Z
            ->orderBy('name')
            // get(): jalankan query, hasilnya Collection berisi semua bahan (tanpa pagination)
            ->get();
    }
}
