<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£'],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥'],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => 'A$'],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'C$'],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHF'],
            ['name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥'],
            ['name' => 'Swedish Krona', 'code' => 'SEK', 'symbol' => 'kr'],
            ['name' => 'New Zealand Dollar', 'code' => 'NZD', 'symbol' => 'NZ$'],
            ['name' => 'Mexican Peso', 'code' => 'MXN', 'symbol' => '$'],
            ['name' => 'Singapore Dollar', 'code' => 'SGD', 'symbol' => 'S$'],
            ['name' => 'Hong Kong Dollar', 'code' => 'HKD', 'symbol' => 'HK$'],
            ['name' => 'Norwegian Krone', 'code' => 'NOK', 'symbol' => 'kr'],
            ['name' => 'South Korean Won', 'code' => 'KRW', 'symbol' => '₩'],
            ['name' => 'Brazilian Real', 'code' => 'BRL', 'symbol' => 'R$'],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
            ['code' => $currency['code']], // Match by unique code
            $currency // Insert or update with this data
            );
        }
    }
}
