<?php

use App\Models\Role;
use App\Models\Timesheet;
use App\Models\User;

it('allows admin to open a user calendar page', function () {
    // Ensure roles exist
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);
    $userRole = Role::firstOrCreate(['name' => 'user'], ['description' => 'Standard User']);

    // Create an admin and a regular user
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();
    $employee->roles()->sync([$userRole->id]);

    // Act as admin and visit the user's calendar
    $this->actingAs($admin);

    $response = $this->get(route('admin.users.calendar', $employee));

    $response->assertSuccessful();
    $response->assertSeeText('User Calendar');
    $response->assertSeeText($employee->name);
});

it('saves hours for a specific date via the calendar form', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    $this->actingAs($admin);

    $month = now()->month;
    $year = now()->year;
    $date = now()->startOfMonth()->toDateString();

    $response = $this->post(route('admin.users.calendar.save', $employee), [
        'month' => $month,
        'year' => $year,
        'hours' => [
            $date => 8,
        ],
    ]);

    $response->assertRedirect(route('admin.users.calendar', ['user' => $employee->id, 'month' => $month, 'year' => $year]));

    $timesheet = Timesheet::where('user_id', $employee->id)
        ->whereDate('date', $date)
        ->where('status', 'draft')
        ->first();

    expect($timesheet)->not->toBeNull();
    expect($timesheet->user_id)->toBe($employee->id);
    expect($timesheet->status)->toBe('draft');
});

it('rounds hours to nearest hour when saving via calendar form', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    /** @var User $employee */
    $employee = User::factory()->create();

    $this->actingAs($admin);

    $month = now()->month;
    $year = now()->year;
    $date = now()->startOfMonth()->toDateString();

    // Test with 7.75 hours - should round to 8 hours
    $response = $this->post(route('admin.users.calendar.save', $employee), [
        'month' => $month,
        'year' => $year,
        'hours' => [
            $date => 7.75,
        ],
    ]);

    $response->assertRedirect();

    $timesheet = Timesheet::where('user_id', $employee->id)
        ->whereDate('date', $date)
        ->first();

    expect($timesheet)->not->toBeNull();

    // Calculate expected hours from start_time and end_time
    $start = \Carbon\Carbon::parse($timesheet->date->toDateString().' '.$timesheet->start_time);
    $end = \Carbon\Carbon::parse($timesheet->date->toDateString().' '.$timesheet->end_time);
    if ($end->lt($start)) {
        $end->addDay();
    }
    $calculatedHours = (int) abs(round($end->diffInHours($start)));

    // Should be 8 hours (rounded from 7.75)
    expect($calculatedHours)->toBe(8);
});
