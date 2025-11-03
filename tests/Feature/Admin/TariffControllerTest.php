<?php

namespace Tests\Feature\Admin;

use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function signInAdmin(): User
    {
        $admin = User::factory()->create();
        // assume roles via gate/permissions; simplest: act as
        $this->actingAs($admin);
        return $admin;
    }

    public function test_index_renders(): void
    {
        $this->signInAdmin();
        $response = $this->get(route('admin.tariffs.index'));
        $response->assertStatus(200);
    }

    public function test_store_creates_tariff(): void
    {
        $this->signInAdmin();

        $payload = [
            'name' => 'Daytime',
            'time_start' => '08:00',
            'time_end' => '17:00',
            'price_per_hour' => 15.25,
            'is_active' => 1,
        ];

        $response = $this->post(route('admin.tariffs.store'), $payload);
        $response->assertRedirect(route('admin.tariffs.index'));
        $this->assertDatabaseHas('tariffs', [
            'name' => 'Daytime',
            'time_start' => '08:00:00',
            'time_end' => '17:00:00',
            'price_per_hour' => 15.25,
            'is_active' => 1,
        ]);
    }

    public function test_store_rejects_overlap(): void
    {
        $this->signInAdmin();

        Tariff::factory()->create([
            'time_start' => '08:00:00',
            'time_end' => '12:00:00',
        ]);

        $payload = [
            'name' => 'Overlap',
            'time_start' => '11:00',
            'time_end' => '13:00',
            'price_per_hour' => 10,
            'is_active' => 1,
        ];

        $response = $this->post(route('admin.tariffs.store'), $payload);
        $response->assertSessionHasErrors('time_start');
        $this->assertDatabaseMissing('tariffs', ['name' => 'Overlap']);
    }
}


