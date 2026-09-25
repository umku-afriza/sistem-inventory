<?php
// File UserFactory: pabrik data user (admin) palsu untuk testing & seeder.
// Factory: pabrik data palsu (dummy). fake() = library Faker untuk membuat data acak yang terlihat asli

// namespace: menunjukkan "alamat"/lokasi class ini (folder database/factories)
namespace Database\Factories;

// use: import class dari namespace lain
use App\Models\User; // model User yang datanya akan dibuat
use Illuminate\Database\Eloquent\Factories\Factory; // class induk semua factory
use Illuminate\Support\Facades\Hash; // Facade Hash: untuk mengenkripsi (hash) password
use Illuminate\Support\Str; // helper Str: fungsi-fungsi pengolah string (teks)

/**
 * @extends Factory<User>
 * extends Factory: UserFactory adalah turunan (inheritance) dari class Factory, khusus untuk model User
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     * protected static ?string: property milik class (static), tipe string atau null (?)
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     * definition(): isi default setiap kolom saat membuat user palsu
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), // nama orang acak
            'email' => fake()->unique()->safeEmail(), // email acak; unique() = tidak ada yang kembar
            'email_verified_at' => now(), // now(): waktu saat ini
            // ??= (null coalescing assignment): hash password hanya sekali, lalu hasilnya dipakai ulang agar cepat
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10), // teks acak 10 karakter
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     * State: variasi data. Pemakaian: User::factory()->unverified()->create()
     */
    public function unverified(): static
    {
        // $this->state(): menimpa sebagian kolom dari definition(). fn (...) => [...] = arrow function
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
