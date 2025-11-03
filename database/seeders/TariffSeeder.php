<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        if (Tariff::count() > 0) {
            return;
        }

        Tariff::create([
            'name' => 'Daytime',
            'time_start' => '08:00:00',
            'time_end' => '18:00:00',
            'price_per_hour' => 15.00,
            'is_active' => true,
        ]);

        Tariff::create([
            'name' => 'Evening',
            'time_start' => '18:00:00',
            'time_end' => '22:00:00',
            'price_per_hour' => 18.00,
            'is_active' => true,
        ]);

        Tariff::create([
            'name' => 'Night',
            'time_start' => '22:00:00',
            'time_end' => '23:59:59',
            'price_per_hour' => 20.00,
            'is_active' => true,
        ]);

        Tariff::create([
            'name' => 'Early Morning',
            'time_start' => '00:00:00',
            'time_end' => '08:00:00',
            'price_per_hour' => 20.00,
            'is_active' => true,
        ]);
    }
}


