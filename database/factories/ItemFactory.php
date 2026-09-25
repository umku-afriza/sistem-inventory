<?php
// File ItemFactory: pabrik data bahan (item) palsu untuk testing & seeder.
// Factory: pabrik data palsu (dummy). fake() = library Faker untuk membuat data acak

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/factories)
namespace Database\Factories;

// use: import class dari namespace lain
use App\Models\Category; // model Category, untuk membuat kategori pemilik bahan
use App\Models\Item; // model Item yang datanya akan dibuat
use Illuminate\Database\Eloquent\Factories\Factory; // class induk semua factory

/**
 * @extends Factory<Item>
 * extends Factory: ItemFactory adalah turunan (inheritance) dari class Factory, khusus untuk model Item
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     * definition(): isi default setiap kolom saat membuat bahan palsu
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Otomatis membuat kategori baru jika tidak ditentukan
            'category_id' => Category::factory(),
            'code' => 'BHN-'.fake()->unique()->numerify('####'), // numerify: # diganti angka acak, contoh BHN-4821
            'name' => ucwords(fake()->words(2, true)), // ucwords: huruf pertama tiap kata jadi kapital
            'unit' => fake()->randomElement(['pcs', 'box', 'rim', 'pak', 'unit']), // randomElement: pilih satu acak dari array
            'stock' => fake()->numberBetween(10, 100), // angka acak antara 10 sampai 100
            'min_stock' => 5, // nilai tetap
            'description' => fake()->sentence(), // satu kalimat acak
        ];
    }

    /**
     * State: bahan dengan stok menipis.
     * Pemakaian: Item::factory()->lowStock()->create()
     * : static = method mengembalikan factory itu sendiri, sehingga bisa disambung (method chaining)
     */
    public function lowStock(): static
    {
        // $this->state(): menimpa sebagian kolom dari definition(). fn (...) => [...] = arrow function
        return $this->state(fn (array $attributes) => [
            'stock' => 2, // stok di bawah min_stock = dianggap menipis
            'min_stock' => 5,
        ]);
    }
}
