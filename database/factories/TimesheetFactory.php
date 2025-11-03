<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Timesheet>
 */
class TimesheetFactory extends Factory
{
    protected $model = Timesheet::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-60 days', 'now');

        // Generate a plausible work period within a day
        $startHour = $this->faker->numberBetween(7, 12);
        $startMinute = Arr::random([0, 15, 30, 45]);
        $durationMinutes = $this->faker->numberBetween(240, 540); // 4h to 9h
        $breakMinutes = $this->faker->randomElement([0, 15, 30, 45, 60]);

        $startTime = sprintf('%02d:%02d:00', $startHour, $startMinute);
        $endTimestamp = (new \DateTime($date->format('Y-m-d') . ' ' . $startTime))
            ->modify("+{$durationMinutes} minutes");
        $endTime = $endTimestamp->format('H:i:s');

        $hours = max(0, round(($durationMinutes - $breakMinutes) / 60, 2));

        $status = $this->faker->randomElement(['draft', 'submitted', 'approved', 'rejected']);

        $approved = $status === 'approved';
        $submitted = in_array($status, ['submitted', 'approved', 'rejected'], true);

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'project_id' => Project::query()->inRandomOrder()->value('id') ?? Project::factory(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'break_duration' => $breakMinutes,
            'note' => $this->faker->boolean(40) ? $this->faker->sentence() : null,
            'status' => $status,
            'submitted_at' => $submitted ? $this->faker->dateTimeBetween($date, 'now') : null,
            'approved_at' => $approved ? $this->faker->dateTimeBetween($date, 'now') : null,
            'approved_by' => $approved ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'timer_started_at' => null,
            'timer_running' => false,
            'calculated_hours' => $hours,
            'total_amount' => $this->faker->randomElement([null, round($hours * $this->faker->numberBetween(20, 120), 2)]),
            'currency' => 'EUR',
        ];
    }
}


