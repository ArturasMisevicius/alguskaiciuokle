<?php

namespace Tests\Unit;

use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffTest extends TestCase
{
    use RefreshDatabase;

    public function test_applies_to_time_within_band(): void
    {
        $tariff = Tariff::factory()->create([
            'time_start' => '08:00:00',
            'time_end' => '12:00:00',
        ]);

        $this->assertTrue($tariff->appliesToTime('09:00:00'));
        $this->assertTrue($tariff->appliesToTime('12:00:00'));
        $this->assertFalse($tariff->appliesToTime('07:59:59'));
        $this->assertFalse($tariff->appliesToTime('12:00:01'));
    }
}


