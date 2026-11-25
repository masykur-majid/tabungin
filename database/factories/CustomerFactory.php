<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genderFaker = fake()->randomElement(['male', 'female']);
        $jenisKelaminDb = match ($genderFaker) {
            'male' => 'Laki-laki',   // atau 'L'
            'female' => 'Perempuan', // atau 'P'
        };
        
        return [
            'nama' => fake()->name($genderFaker),
            'nik' => fake()->numerify('################'),
            'asal_sekolah' => 'SMKS 1 Parahyangan',
            'jenis_kelamin' => $jenisKelaminDb,
            'alamat_rumah' => fake()->address(),
            'no_telepon' => fake() ->phoneNumber(),

        ];
    }
}
