<?php

namespace Database\Factories;

use App\Models\Dorm;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dorm_id' => Dorm::factory(),
            'number' => (string) fake()->unique()->numberBetween(101, 120),
            'capacity' => fake()->randomElement([2, 3, 4]),
        ];
    }
}
