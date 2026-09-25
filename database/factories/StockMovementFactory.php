<?php
// File StockMovementFactory: pabrik data transaksi stok (masuk/keluar) palsu untuk testing & seeder.
// Factory: pabrik data palsu (dummy). fake() = library Faker untuk membuat data acak

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/factories)
namespace Database\Factories;

// use: import class dari namespace lain
use App\Enums\MovementType; // Enum jenis transaksi: In (masuk) / Out (keluar)
use App\Models\Item; // model Item (bahan yang ditransaksikan)
use App\Models\StockMovement; // model StockMovement yang datanya akan dibuat
use App\Models\User; // model User (admin yang mencatat)
use Illuminate\Database\Eloquent\Factories\Factory; // class induk semua factory

/**
 * @extends Factory<StockMovement>
 * extends Factory: StockMovementFactory adalah turunan (inheritance) dari class Factory, khusus untuk model StockMovement
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     * definition(): isi default setiap kolom saat membuat transaksi palsu
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(), // otomatis membuat bahan baru jika tidak ditentukan
            'user_id' => User::factory(), // otomatis membuat user baru jika tidak ditentukan
            'type' => fake()->randomElement(MovementType::cases()), // cases(): semua pilihan enum, lalu dipilih satu acak
            'quantity' => fake()->numberBetween(1, 10), // angka acak 1 sampai 10
            'moved_at' => fake()->dateTimeBetween('-1 month'), // tanggal acak antara 1 bulan lalu sampai sekarang
            'note' => fake()->sentence(), // satu kalimat acak
        ];
    }
}
