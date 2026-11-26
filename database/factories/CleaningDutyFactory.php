<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\CleaningDuty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CleaningDuty> */
class CleaningDutyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'area' => fake()->randomElement([
                'Dormitory hallway',
                'Shared bathroom',
                'Common room',
                'Study area',
            ]),
            'user_id' => User::factory()->state([
                'role' => Role::Student,
            ]),
            'due_date' => fake()->dateTimeBetween('today', '+14 days'),
            'completed_at' => null,
        ];
    }
}
