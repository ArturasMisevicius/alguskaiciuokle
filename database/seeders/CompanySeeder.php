<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        if (Company::count() > 0) {
            return;
        }

        Company::create([
            'name' => 'Default Company',
            'code' => 'CMP-DEFAULT',
        ]);
    }
}



