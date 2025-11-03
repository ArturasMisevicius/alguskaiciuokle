<?php

namespace Database\Seeders;

use App\Models\RateCard;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RateCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRole = Role::where('name', 'user')->first();

        $rateCards = [
            [
                'name' => 'Standard Rate - Regular Hours',
                'role_id' => $userRole->id,
                'days_of_week' => [1, 2, 3, 4, 5], // Mon-Fri
                'time_start' => '08:00',
                'time_end' => '18:00',
                'rate_type' => 'fixed',
                'rate_amount' => 15.00,
                'currency' => 'EUR',
                'is_overtime' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Evening Rate - Weekdays',
                'role_id' => $userRole->id,
                'days_of_week' => [1, 2, 3, 4, 5], // Mon-Fri
                'time_start' => '18:00',
                'time_end' => '22:00',
                'rate_type' => 'fixed',
                'rate_amount' => 20.00, // Evening premium
                'currency' => 'EUR',
                'is_overtime' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Weekend Rate',
                'role_id' => $userRole->id,
                'days_of_week' => [6, 7], // Sat-Sun
                'rate_type' => 'fixed',
                'rate_amount' => 25.00, // Weekend premium
                'currency' => 'EUR',
                'is_overtime' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Daily Overtime (>8 hours)',
                'role_id' => $userRole->id,
                'rate_type' => 'fixed',
                'rate_amount' => 22.50, // 1.5x standard rate
                'currency' => 'EUR',
                'is_overtime' => true,
                'overtime_type' => 'daily',
                'overtime_threshold' => 8.00,
                'is_active' => true,
            ],
        ];

        foreach ($rateCards as $rateCard) {
            // Check if a similar rate card exists to avoid duplicates
            $exists = RateCard::where('name', $rateCard['name'])->exists();
            if (!$exists) {
                RateCard::create($rateCard);
            }
        }
    }
}
