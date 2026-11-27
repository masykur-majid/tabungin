<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         
        //generate dummy user data
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'petugas 1',
                'email' => 'petugas1@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('petugas123'),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'supervisor 1',
                'email' => 'supervisor1@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('supervisor123'),
                'remember_token' => Str::random(10),
            ],

        ];

        foreach ($users as $user) {
            User::create($user);
        }
        
        //generate dummy data for customers table
        Customer::factory(10)->create();


        //generate dummy data for Accounts table
        $customerId = Customer::pluck('id');
        
        foreach($customerId as $id){
            Account::factory()->create([
                'customer_id' => $id,
            ]);
        }
        
        //generate dummy data for transaction with yesterday date
        Transaction::factory(5)->create();

        //generate dummy data for transaction with yesterday date
        // Transaction::factory(5)->yesterday()->create();
    }
}
