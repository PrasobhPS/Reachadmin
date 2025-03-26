<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyExchangeRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currency_exchange_rates')->insert([
            [
                'currency_code' => 'USD',
                'exchange_rate_to_usd' => 1.000000,  // USD to USD = 1
                'exchange_rate_to_gbp' => 0.7462,    // Example: USD to GBP
                'exchange_rate_to_eur' => 0.9231,    // Example: USD to EUR
            ],
            [
                'currency_code' => 'GBP',
                'exchange_rate_to_usd' => 1.3416,    // Example: GBP to USD
                'exchange_rate_to_gbp' => 1.000000,  // GBP to GBP = 1
                'exchange_rate_to_eur' => 1.2390,    // Example: GBP to EUR
            ],
            [
                'currency_code' => 'EUR',
                'exchange_rate_to_usd' => 1.0849,    // Example: EUR to USD
                'exchange_rate_to_gbp' => 0.8070,    // Example: EUR to GBP
                'exchange_rate_to_eur' => 1.000000,  // EUR to EUR = 1
            ],
        ]);
    }
}
