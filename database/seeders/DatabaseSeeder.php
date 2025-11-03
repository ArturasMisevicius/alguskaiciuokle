<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order of dependencies
        $this->call([
            CompanySeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            RateCardSeeder::class,
            TariffSeeder::class,
            TimesheetSeeder::class,
        ]);
    }
}
