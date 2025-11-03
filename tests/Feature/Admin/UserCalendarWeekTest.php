<?php

use App\Models\Role;
use App\Models\Timesheet;
use App\Models\TimesheetPricingDetail;
use App\Models\Tariff;
use App\Models\User;

it('allows admin to open a weekly user calendar page', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    $this->actingAs($admin);

    $response = $this->get(route('admin.users.calendar', ['user' => $employee->id, 'view' => 'week']));

    $response->assertSuccessful();
    $response->assertSeeText('User Calendar — Week');
});

it('saves hours for a specific week via the weekly calendar', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    $this->actingAs($admin);

    $anchor = now()->startOfWeek();
    $date = $anchor->toDateString();

    $response = $this->post(route('admin.users.calendar.save', $employee), [
        'month' => $anchor->month,
        'year' => $anchor->year,
        'hours' => [
            $date => 6,
        ],
    ]);

    $response->assertRedirect();

    $timesheet = Timesheet::where('user_id', $employee->id)
        ->whereDate('date', $date)
        ->first();

    expect($timesheet)->not->toBeNull();
});

it('month view shows Week view toggle button', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    $this->actingAs($admin);

    $response = $this->get(route('admin.users.calendar', $employee));

    $response->assertSuccessful();
    $response->assertSee('Week view');
});

it('saves hours with selected tariff for a day', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    // Create a tariff
    /** @var Tariff $tariff */
    $tariff = Tariff::create([
        'name' => 'Daytime',
        'time_start' => '08:00:00',
        'time_end' => '18:00:00',
        'price_per_hour' => 25.50,
        'is_active' => true,
    ]);

    $this->actingAs($admin);

    $date = now()->startOfMonth()->toDateString();
    $month = now()->month;
    $year = now()->year;

    $response = $this->post(route('admin.users.calendar.save', $employee), [
        'month' => $month,
        'year' => $year,
        'hours' => [
            $date => 4,
        ],
        'tariff' => [
            $date => $tariff->id,
        ],
    ]);

    $response->assertRedirect();

    /** @var Timesheet $ts */
    $ts = Timesheet::where('user_id', $employee->id)->whereDate('date', $date)->first();
    expect($ts)->not->toBeNull();
    expect((float) $ts->calculated_hours)->toBe(4.0);
    expect((float) $ts->total_amount)->toBe(4 * 25.50);

    /** @var TimesheetPricingDetail $detail */
    $detail = TimesheetPricingDetail::where('timesheet_id', $ts->id)->first();
    expect($detail)->not->toBeNull();
    expect((float) $detail->applied_rate)->toBe((float) $tariff->price_per_hour);
    expect((float) $detail->segment_hours)->toBe(4.0);
});
