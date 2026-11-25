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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'student_id' => User::factory()->state([
                'role' => Role::Student,
                'room_id' => null,
            ]),
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
