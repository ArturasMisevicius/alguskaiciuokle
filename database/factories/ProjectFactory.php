<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->unique()->words(3, true),
            'code' => strtoupper($this->faker->bothify('PRJ-###')),
            'description' => $this->faker->optional()->sentence(8),
            'is_active' => $this->faker->boolean(85),
        ];
    }
}


