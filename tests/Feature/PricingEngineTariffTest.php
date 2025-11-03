<?php

namespace Tests\Feature;

use App\Models\Tariff;
use App\Models\Timesheet;
use App\Models\User;
use App\Services\PricingEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingEngineTariffTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_uses_tariff_when_no_rate_card_matches(): void
    {
        $user = User::factory()->create();
        $timesheet = Timesheet::create([
            'user_id' => $user->id,
            'project_id' => null,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'break_duration' => 0,
            'status' => 'approved',
            'currency' => 'EUR',
        ]);

        Tariff::create([
            'name' => 'Morning',
            'time_start' => '08:00:00',
            'time_end' => '12:00:00',
            'price_per_hour' => 20.00,
            'is_active' => true,
        ]);

        // No RateCards set up on purpose
        $service = new PricingEngineService;
        $service->calculatePricing($timesheet->fresh());

        $timesheet->refresh();
        $this->assertEquals(2.00, (float) $timesheet->calculated_hours);
        $this->assertEquals(40.00, (float) $timesheet->total_amount);

        $this->assertDatabaseHas('timesheet_pricing_details', [
            'timesheet_id' => $timesheet->id,
            'segment_hours' => 2.00,
            'segment_amount' => 40.00,
        ]);
    }
}
