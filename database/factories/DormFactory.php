<?php

namespace Database\Factories;

use App\Models\Dorm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dorm>
 */
class DormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Eve Hall',
                'Esther Hall',
                'Ruth Hall',
                'Elijah Hall',
                'Solomon Hall',
                'Sarah Hall',
            ]),
        ];
    }
}
