<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::inRandomOrder()->first()?->id,
            'user_id' => 2,
            'jumlah_transaksi' =>fake()->numberBetween(2, 100)*1000,
        ];
    }
}
