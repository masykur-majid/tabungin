<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerId = Customer::pluck('id');

        foreach($customerId as $id){
            Account::factory()->create([
                'customer_id' => $id,
            ]);
        }
    }
}
