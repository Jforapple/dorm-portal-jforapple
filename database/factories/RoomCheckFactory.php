<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomCheck>
 */
class RoomCheckFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),

            'student_id' => User::factory()->state([
                'role' => Role::Student,
                'room_id' => null,
            ]),

            'inspection_date' => fake()->dateTimeBetween('-30 days', 'now'),

            'inspector_id' => User::factory()->state([
                'role' => Role::Staff,
            ]),

            'checklist' => [
                'bed' => fake()->randomElement(['pass', 'fail']),
                'floor' => fake()->randomElement(['pass', 'fail']),
                'desk' => fake()->randomElement(['pass', 'fail']),
                'trash' => fake()->randomElement(['pass', 'fail']),
                'bathroom' => fake()->randomElement(['pass', 'fail']),
            ],

            'score' => fake()->numberBetween(0, 100),

            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (RoomCheck $roomCheck): void {
            $roomCheck->student()->update([
                'room_id' => $roomCheck->room_id,
            ]);
        });
    }
}
