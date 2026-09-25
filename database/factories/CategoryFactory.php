<?php
// File CategoryFactory: pabrik data kategori palsu untuk testing & seeder.
// Factory: pabrik data palsu (dummy). fake() = library Faker untuk membuat data acak

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/factories)
namespace Database\Factories;

// use: import class dari namespace lain
use App\Models\Category; // model Category yang datanya akan dibuat
use Illuminate\Database\Eloquent\Factories\Factory; // class induk semua factory

/**
 * @extends Factory<Category>
 * extends Factory: CategoryFactory adalah turunan (inheritance) dari class Factory, khusus untuk model Category
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     * definition(): isi default setiap kolom saat membuat kategori palsu
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true), // 2 kata acak dalam bentuk teks; unique() = tidak kembar
            'description' => fake()->sentence(), // satu kalimat acak
        ];
    }
}
