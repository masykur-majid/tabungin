<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        
        return [
            'customer_id' => Customer::factory(),
            'nomor_rekening' => fake()->numerify('###############'),
            'saldo' => 10000,
            'is_active' => true,
        ];
    }
}
