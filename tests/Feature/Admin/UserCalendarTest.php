<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Timesheet;

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

    $this->assertDatabaseHas('timesheets', [
        'user_id' => $employee->id,
        'date' => $date,
        'status' => 'draft',
    ]);
});


