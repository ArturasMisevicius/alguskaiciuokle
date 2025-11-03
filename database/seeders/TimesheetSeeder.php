<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder
{
    /**
     * Seed fake timesheets for admin listing.
     */
    public function run(): void
    {
        // Ensure there are users and projects to relate to
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }
        if (Project::count() === 0) {
            Project::factory()->count(3)->create();
        }

        // Create a reasonable volume of records if table is empty
        if (Timesheet::count() === 0) {
            Timesheet::factory()->count(80)->create();
        } else {
            // Top-up a bit to give fresh data variety
            Timesheet::factory()->count(20)->create();
        }
    }
}
