<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::query()->first() ?? Company::factory()->create(['name' => 'Default Company', 'code' => 'CMP-DEFAULT']);

        $projects = [
            [
                'name' => 'Internal Development',
                'code' => 'INT-DEV',
                'description' => 'Internal company development projects',
                'is_active' => true,
                'company_id' => $company->id,
            ],
            [
                'name' => 'Client Project A',
                'code' => 'CLIENT-A',
                'description' => 'Main client project A',
                'is_active' => true,
                'company_id' => $company->id,
            ],
            [
                'name' => 'Client Project B',
                'code' => 'CLIENT-B',
                'description' => 'Client project B - consulting',
                'is_active' => true,
                'company_id' => $company->id,
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINT',
                'description' => 'General maintenance and support',
                'is_active' => true,
                'company_id' => $company->id,
            ],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['code' => $project['code']],
                $project
            );
        }
    }
}
