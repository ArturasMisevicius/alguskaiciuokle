<?php

namespace Database\Factories;

use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tariff>
 */
class TariffFactory extends Factory
{
    protected $model = Tariff::class;

    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(0, 20);
        $endHour = $startHour + $this->faker->numberBetween(1, 4);

        return [
            'name' => 'Tariff ' . $this->faker->word(),
            'time_start' => sprintf('%02d:00:00', $startHour),
            'time_end' => sprintf('%02d:00:00', min(23, $endHour)),
            'price_per_hour' => $this->faker->randomFloat(2, 5, 100),
            'is_active' => true,
        ];
    }
}


